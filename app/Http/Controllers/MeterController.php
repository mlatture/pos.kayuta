<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

use App\Models\Readings;
use App\Models\Site;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Bills;
use App\Models\BusinessSettings;
use App\Models\ElectricBill;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\ElectricBillGenerated;


use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MeterController extends Controller
{
    public function index()
    {
        $thresholdDate = Carbon::now()->subDays(20)->toDateString();

        $overDueMeterNumbers = Readings::select('meter_number')
            ->groupBy('meter_number')
            ->havingRaw('MAX(date) <= ?', [$thresholdDate])
            ->pluck('meter_number');

        $overdueSites = Site::whereIn('meter_number', $overDueMeterNumbers)->get();

        $getLatestMN = Readings::whereIn('id', function ($q) {
            $q->selectRaw('MAX(id)')->from('electric_readings')->groupBy('meter_number');
        })->get();

        return view('meters.index', compact('overdueSites', 'getLatestMN'));
    }

    private function getClaudePromptMessage(string $type = 'meter_page'): string
    {
        $template = DB::table('prompt_templates')->where('type', $type)->select('user_prompt')->first();

        $userPrompt = trim($template->user_prompt ?? '');

        return $userPrompt;
    }

    public function read(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $path = Readings::storeFile($request->file('photo'));
        } else {
            return back()->with('warning', 'Please choose a photo.');
        }

        if (!$path || !file_exists(public_path('storage/' . $path))) {
            return back()->with('error', 'Image not found.');
        }

        $imagePath = public_path('storage/' . $path);
        $imageBytes = @file_get_contents($imagePath);
        if ($imageBytes === false) {
            return back()->with('error', 'Unable to read uploaded image.');
        }

        $base64 = base64_encode($imageBytes);
        $mimeType = $request->file('photo')->getMimeType();

        $textPrompt = $this->getClaudePromptMessage('meter_page');

        $payload = [
            'model' => 'claude-sonnet-4-20250514',
            'max_tokens' => 400,
            'temperature' => 0.1,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $textPrompt],
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => $mimeType,
                                'data' => $base64,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // 4) API call with enhanced error handling
        $maxRetries = 10;
        $attempt = 0;

        do {
            $attempt++;
            $t0 = microtime(true);

            try {
                $response = Http::timeout(45)
                    ->withHeaders([
                        'x-api-key' => env('CLAUDE_API_KEY'),
                        'anthropic-version' => '2023-06-01',
                    ])
                    ->post('https://api.anthropic.com/v1/messages', $payload);

                $latencyMs = (int) ((microtime(true) - $t0) * 1000);

                if ($response->ok()) {
                    $result = $this->processAIResponse($response->json(), $path, $latencyMs, $attempt);

                    if ($result['success']) {
                        return $result['response'];
                    }

                    // If parsing failed but we have retries left, modify prompt based on error type
                    if ($attempt < $maxRetries) {
                        $errorMsg = implode(', ', $result['errors'] ?? []);

                        if (strpos($errorMsg, 'Meter number') !== false) {
                            // Specific retry for meter number issues
                            $payload['messages'][0]['content'][0]['text'] = "RETRY ATTEMPT {$attempt}: You failed to find the meter serial number. " . 'LOOK CAREFULLY at the white label below the black kWh counter. ' . "The meter serial number is printed on this white label, usually 8+ digits like '80678828'. " . 'DO NOT use the kWh reading (04684) as the meter number. ' . $textPrompt;
                        } else {
                            // General retry
                            $payload['messages'][0]['content'][0]['text'] = "RETRY ATTEMPT {$attempt}: Previous error: {$errorMsg}. " . $textPrompt . "\n\nBe extra careful with number identification.";
                        }
                    }
                } else {
                    Log::warning("Claude API HTTP error on attempt {$attempt}", [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (Exception $e) {
                Log::error("Claude API exception on attempt {$attempt}", [
                    'error' => $e->getMessage(),
                ]);
            }

            if ($attempt < $maxRetries) {
                sleep(1); // Brief delay between retries
            }
        } while ($attempt < $maxRetries);

        // All attempts failed
        Log::error('Claude API failed after all retries', [
            'attempts' => $maxRetries,
            'image_path' => $path,
        ]);

        return back()->with('error', 'AI scanning failed after multiple attempts. Please ensure the meter image is clear and try again.');
    }

    private function processAIResponse(array $data, string $path, int $latencyMs, int $attempt): array
    {
        $contentBlocks = $data['content'] ?? [];
        $rawText = '';

        foreach ($contentBlocks as $blk) {
            if (($blk['type'] ?? '') === 'text') {
                $rawText .= $blk['text'] . "\n";
            }
        }

        // Enhanced JSON extraction
        $jsonPattern = '/\{(?:[^{}]|(?:\{[^{}]*\}))*\}/s';
        if (!preg_match($jsonPattern, $rawText, $matches)) {
            Log::warning("No JSON found in AI response (attempt {$attempt})", [
                'raw_text' => $rawText,
            ]);
            return ['success' => false, 'error' => 'No JSON in response'];
        }

        $parsed = json_decode($matches[0], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning("Invalid JSON from AI (attempt {$attempt})", [
                'json_error' => json_last_error_msg(),
                'raw_json' => $matches[0],
            ]);
            return ['success' => false, 'error' => 'Invalid JSON format'];
        }

        // Enhanced validation
        $validation = $this->validateMeterData($parsed);
        if (!$validation['valid']) {
            Log::warning("AI data validation failed (attempt {$attempt})", [
                'validation_errors' => $validation['errors'],
                'parsed_data' => $parsed,
            ]);
            return ['success' => false, 'error' => implode(', ', $validation['errors'])];
        }

        // Extract validated data
        $aiMeterNumber = preg_replace('/\D/', '', trim($parsed['meter_number'] ?? ''));
        $aiReading = (float) ($parsed['reading'] ?? 0);
        $manufacturer = trim($parsed['manufacturer'] ?? '');
        $meterStyle = trim($parsed['meter_style'] ?? '');
        $confidence = trim($parsed['confidence'] ?? 'medium');
        $notes = trim($parsed['notes'] ?? '');

        // 5) Database operations (same as your existing logic)
        $site = Site::where('meter_number', $aiMeterNumber)->first();

        $readingRow = Readings::create([
            'meter_number' => $aiMeterNumber,
            'kwhNo' => $aiReading,
            'image' => $path,
            'date' => now()->toDateString(),
            'siteid' => $site->siteid ?? null,
            'ai_meter_number' => $aiMeterNumber,
            'ai_meter_reading' => $aiReading,
            'ai_success' => true,
            'ai_fixed' => false,
            'manufacturer' => $manufacturer ?: null,
            'meter_style' => $meterStyle ?: null,
            'ai_confidence' => $confidence,
            'ai_notes' => $notes,
            'ai_attempts' => $attempt,
            'prompt_version' => 'optimized/v2',
            'model_version' => 'claude-sonnet-4-20250514',
            'ai_latency_ms' => $latencyMs,
        ]);

        $last = Readings::where('meter_number', $aiMeterNumber)->where('id', '<', $readingRow->id)->latest('date')->first();

        $previousKwh = $last?->kwhNo ?? 0;
        $previousDate = $last?->date ?? now();
        $days = max(1, now()->diffInDays(Carbon::parse($previousDate)));
        $usage = $aiReading - $previousKwh;

        $rate = (float) (BusinessSettings::where('type', 'electric_meter_rate')->value('value') ?? 0);
        $total = $usage * $rate;

        $thirtyDayCap = (float) (BusinessSettings::where('type', 'electric_bill_high_threshold_30day')->value('value') ?? 150);
        $threshold = ($thirtyDayCap / 30.0) * $days;

        $reservation = null;
        $customer = null;
        if ($site) {
            $reservation = Reservation::where('siteid', $site->siteid)->latest()->first();
            $customer = $reservation ? User::find($reservation->customernumber) : null;
        }

        $isNewMeter = !$site;

        // 7) Build preview response
        $reading = (object) [
            'id' => $readingRow->id,
            'kwhNo' => $readingRow->kwhNo,
            'meter_number' => $readingRow->meter_number,
            'ai_meter_number' => $readingRow->ai_meter_number,
            'ai_meter_reading' => $readingRow->ai_meter_reading,
            'ai_confidence' => $confidence,
            'ai_notes' => $notes,
            'ai_attempts' => $attempt,
            'image' => $readingRow->image,
            'date' => $readingRow->date,
            'siteid' => $readingRow->siteid,
            'usage' => $usage,
            'rate' => $rate,
            'total' => $total,
            'previousKwh' => $previousKwh,
            'new_meter_number' => $isNewMeter,
            'days' => $days,
            'threshold' => $threshold,
        ];


        return [
            'success' => true,
            'response' => view('meters.preview', [
                'reading' => $reading,
                'site' => $site,
                'customer' => $customer,
                'customer_name' => $customer ? trim($customer->f_name . ' ' . $customer->l_name) : null,
                'start_date' => Carbon::parse($previousDate)->toDateString(),
                'end_date' => now()->toDateString(),
                'reservation_id' => $reservation->id ?? '',
            ]),
        ];
    }

    private function validateMeterData(array $data): array
    {
        $errors = [];

        $meterNumber = preg_replace('/\D/', '', trim($data['meter_number'] ?? ''));
        if (!$meterNumber) {
            $errors[] = 'Missing meter number';
        } elseif (strlen($meterNumber) < 6 || strlen($meterNumber) > 12) {
            $errors[] = 'Invalid meter number length (should be 6-12 digits)';
        }

        // Validate reading
        $reading = $data['reading'] ?? null;
        if ($reading === null || !is_numeric($reading)) {
            $errors[] = 'Missing or invalid kWh reading';
        } elseif ((float) $reading < 0 || (float) $reading > 999999) {
            $errors[] = 'kWh reading out of reasonable range (0-999999)';
        }

        // Validate confidence level
        $confidence = strtolower(trim($data['confidence'] ?? ''));
        if (!in_array($confidence, ['high', 'medium', 'low'])) {
            $errors[] = 'Invalid confidence level';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    public function scan(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $path = Readings::storeFile($request->file('photo'));
        } else {
            return back()->with('warning', 'Please choose a photo.');
        }

        if (!$path || !file_exists(public_path('storage/' . $path))) {
            return back()->with('error', 'Image not found.');
        }

        $imagePath = public_path('storage/' . $path);
        $imageBytes = @file_get_contents($imagePath);
        if ($imageBytes === false) {
            return back()->with('error', 'Unable to read uploaded image.');
        }

        $base64 = base64_encode($imageBytes);
        $mimeType = $request->file('photo')->getMimeType();

        $textPrompt = $this->getClaudePromptMessage('meter_page');

        $payload = [
            'model' => 'claude-sonnet-4-20250514',
            'max_tokens' => 400,
            'temperature' => 0.1,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $textPrompt],
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => $mimeType,
                                'data' => $base64,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // 4) API call with enhanced error handling
        $maxRetries = 3;
        $attempt = 0;

        do {
            $attempt++;
            $t0 = microtime(true);

            try {
                $response = Http::timeout(45)
                    ->withHeaders([
                        'x-api-key' => env('CLAUDE_API_KEY'),
                        'anthropic-version' => '2023-06-01',
                    ])
                    ->post('https://api.anthropic.com/v1/messages', $payload);

                $latencyMs = (int) ((microtime(true) - $t0) * 1000);

                if ($response->ok()) {
                    $result = $this->processAIResponse($response->json(), $path, $latencyMs, $attempt);

                    if ($result['success']) {
                        return $result['response'];
                    }

                    // If parsing failed but we have retries left, modify prompt based on error type
                    if ($attempt < $maxRetries) {
                        $errorMsg = implode(', ', $result['errors'] ?? []);

                        if (strpos($errorMsg, 'Meter number') !== false) {
                            // Specific retry for meter number issues
                            $payload['messages'][0]['content'][0]['text'] = "RETRY ATTEMPT {$attempt}: You failed to find the meter serial number. " . 'LOOK CAREFULLY at the white label below the black kWh counter. ' . "The meter serial number is printed on this white label, usually 8+ digits like '80678828'. " . 'DO NOT use the kWh reading (04684) as the meter number. ' . $textPrompt;
                        } else {
                            // General retry
                            $payload['messages'][0]['content'][0]['text'] = "RETRY ATTEMPT {$attempt}: Previous error: {$errorMsg}. " . $textPrompt . "\n\nBe extra careful with number identification.";
                        }
                    }
                } else {
                    Log::warning("Claude API HTTP error on attempt {$attempt}", [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (Exception $e) {
                Log::error("Claude API exception on attempt {$attempt}", [
                    'error' => $e->getMessage(),
                ]);
            }

            if ($attempt < $maxRetries) {
                sleep(1); // Brief delay between retries
            }
        } while ($attempt < $maxRetries);

        // All attempts failed
        Log::error('Claude API failed after all retries', [
            'attempts' => $maxRetries,
            'image_path' => $path,
        ]);

        return back()->with('error', 'AI scanning failed after multiple attempts. Please ensure the meter image is clear and try again.');
    }

    public function unregister(Request $request)
    {
        return view('meters.unregistered', [
            'meter_number' => $request->meter_number,
            'reading' => $request->reading,
            'image' => $request->image,
            'date' => $request->date,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'siteid' => 'required|string|exists:sites,siteid',
            'meter_number' => 'required|string',
            'kwhNo' => 'required|numeric',
            'image' => 'required|string',
            'date' => 'required|date',
        ]);

        $newMeterNumber = $request->meter_number;
        $site = Site::where('siteid', $request->siteid)->firstOrFail();

        $conflictSite = Site::where('meter_number', $newMeterNumber)->where('siteid', '!=', $site->siteid)->first();

        if ($conflictSite) {
            $conflictSite->meter_number = null;
            $conflictSite->save();
        }

        $site->meter_number = $newMeterNumber;
        $site->save();

        return redirect()
            ->route('meters.preview.fromSession')
            ->with('reading_data', [
                'kwhNo' => $request->kwhNo,
                'meter_number' => $newMeterNumber,
                'image' => $request->image,
                'date' => $request->date,
                'siteid' => $site->siteid,
            ]);
    }

    public function previewFromSession()
    {
        $data = session('reading_data');

        if (!$data) {
            return redirect()->route('meters.index')->with('error', 'No reading data available.');
        }

        $site = Site::where('siteid', $data['siteid'])->first();
        $lastReading = Readings::where('meter_number', $data['meter_number'])->latest('date')->first();

        $previousKwh = $lastReading?->kwhNo ?? 0;
        $previousDate = $lastReading?->date ?? now();
        $usage = $data['kwhNo'] - $previousKwh;
        $days = now()->diffInDays(\Carbon\Carbon::parse($previousDate));
        $rate = BusinessSettings::where('type', 'electric_meter_rate')->value('value');

        $total = $usage * $rate;

        $reservation = Reservation::where('siteid', $data['siteid'])->whereDate('cid', '<=', now())->whereDate('cod', '>=', now())->first();

        $customer = $reservation ? User::find($reservation->customernumber) : null;

        $reading = (object) [
            'kwhNo' => $data['kwhNo'],
            'meter_number' => $data['meter_number'],
            'image' => $data['image'],
            'date' => $data['date'],
            'siteid' => $data['siteid'],
            'usage' => $usage,
            'rate' => $rate,
            'total' => $total,
            'previousKwh' => $previousKwh,
            'new_meter_number' => true,
        ];

        return view('meters.preview', [
            'reading' => $reading,
            'site' => $site,
            'customer' => $customer,
            'customer_name' => $customer ? trim($customer->f_name . ' ' . $customer->l_name) : null,
            'start_date' => Carbon::parse($previousDate)->toDateString(),
            'end_date' => now()->toDateString(),
            'days' => $days,
            'reservation_id' => $reservation->id ?? '',
        ]);
    }

    public function send(Request $r)
    {
        $r->validate([
            'reading_id' => 'required|exists:electric_readings,id',
            'meter_number' => 'required|string',
            'kwhNo' => 'required|numeric',
            'prevkwhNo' => 'nullable|numeric',
            'rate' => 'required|numeric',
            'days' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'customer_id' => 'nullable|exists:users,id',
            'override_send' => 'nullable|boolean',
            'ai_success' => 'required',
            'ai_fixed' => 'nullable',
            'training_opt_in' => 'nullable|boolean',
            'new_meter_number' => 'required|boolean',
            'siteid' => 'nullable|string', 
        ]);

        $reading = Readings::findOrFail($r->reading_id);

        $reading->meter_number = preg_replace('/\D/', '', $r->meter_number);
        $reading->kwhNo = (float) $r->kwhNo;
        $reading->ai_success = filter_var($r->ai_success, FILTER_VALIDATE_BOOLEAN);
        $reading->ai_fixed = filter_var($r->ai_fixed ?? false, FILTER_VALIDATE_BOOLEAN);
        $reading->training_opt_in = filter_var($r->training_opt_in ?? false, FILTER_VALIDATE_BOOLEAN);
        $reading->save();

        if ($r->boolean('new_meter_number') === true) {
            $this->logAudit('Bill Validation', 'New meter detected: billing blocked', [
                'reading_id' => $reading->id,
                'meter_number' => $reading->meter_number,
            ]);
            return back()->with('warning', 'New meter detected. You can Save, but cannot Send.');
        }

        $usage = (float) $r->kwhNo - (float) ($r->prevkwhNo ?? 0);
        $total = $usage * (float) $r->rate;

        if ($total <= 0) {
            $this->logAudit('Bill Validation', 'Zero/negative bill blocked', [
                'reading_id' => $reading->id,
                'total' => $total,
            ]);
            return back()->with('error', 'Total is zero or negative. You can Save, but cannot Send.');
        }

        $cap30 = (float) (DB::table('business_settings')->where('type', 'electric_bill_high_threshold_30day')->value('value') ?? 150);
        $threshold = ($cap30 / 30.0) * (int) $r->days;
        $override = (bool) $r->boolean('override_send');

        if ($total > $threshold && !$override) {
            $this->logAudit('Bill Validation', 'Threshold exceeded; override required', [
                'reading_id' => $reading->id,
                'total' => $total,
                'threshold' => $threshold,
            ]);
            return back()->with('warning', 'Bill exceeds threshold. Tick "Send anyway?" to proceed.');
        }

        $meter = $reading->meter_number;
        $contextSiteId = $r->siteid ?: null;
        $currentAssigned = Site::where('meter_number', $meter)->first();

        if ($contextSiteId) {
            if ($currentAssigned && $currentAssigned->siteid !== $contextSiteId) {
                DB::transaction(function () use ($currentAssigned, $contextSiteId, $meter, $reading) {
                    $old = $currentAssigned->siteid;
                    $currentAssigned->update(['meter_number' => null]);
                    Site::where('siteid', $contextSiteId)->update(['meter_number' => $meter]);
                    $this->logAudit('Meter Reassigned', 'Meter moved between sites', [
                        'meter_number' => $meter,
                        'from_site' => $old,
                        'to_site' => $contextSiteId,
                        'reading_id' => $reading->id,
                    ]);
                });
            } elseif (!$currentAssigned) {
                Site::where('siteid', $contextSiteId)->update(['meter_number' => $meter]);
                $this->logAudit('Meter Reassigned', 'Meter assigned to site', [
                    'meter_number' => $meter,
                    'to_site' => $contextSiteId,
                    'reading_id' => $reading->id,
                ]);
            }
        } else {
            if ($currentAssigned) {
            } else {
                $this->logAudit('Bill Validation', 'No site context for meter during send', [
                    'meter_number' => $meter,
                    'reading_id' => $reading->id,
                ]);
            }
        }

        $bill = ElectricBill::create([
            'reading_id' => $reading->id,
            'meter_number' => $meter,
            'customer_id' => $r->customer_id,
            'start_date' => $r->start_date,
            'end_date' => $r->end_date,
            'usage_kwh' => $usage,
            'rate' => (float) $r->rate,
            'total' => $total,
            'threshold_used' => $threshold,
            'warning_overridden' => $override,
            'sent_at' => now(),
        ]);

        $this->logAudit('Bill Validation', 'Bill sent', [
            'bill_id' => $bill->id,
            'reading_id' => $reading->id,
            'total' => $total,
            'threshold' => $threshold,
            'override' => $override,
        ]);


        return redirect()->route('meters.index')->with('success', 'Bill saved and sent.');
    }

    private function logAudit(string $type, string $message, array $meta = []): void
    {
        DB::table('system_logs')->insert([
            'transaction_type' => $type,
            'description' => $message,
            'before' => json_encode($meta),
            'after' => json_encode($meta),
            'created_at' => now(),
            
        ]);
    }

    private function systemLog(string $type, array $payload = []): void
    {
        try {
            DB::table('system_logs')->insert([
                'transaction_type' => $type,
                'payload' => json_encode($payload),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('system_log insert failed', ['type' => $type, 'err' => $e->getMessage()]);
        }
    }
}
