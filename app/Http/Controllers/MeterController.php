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
use Illuminate\Support\Facades\Schema;

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
                    $result = $this->processAIResponse($response->json(), $path, $latencyMs, $attempt, $request);

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

    private function processAIResponse(array $data, string $path, int $latencyMs, int $attempt, string $request): array
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

        // 5) No Database operations
        $site = Site::where('meter_number', $aiMeterNumber)->first();
        $last = Readings::where('meter_number', $aiMeterNumber)->latest('date')->first();
        $previousKwh = $last?->kwhNo ?? 0;
        $previousDate = $last?->date ?? now();
        $days = max(1, now()->diffInDays(Carbon::parse($previousDate)));
        $usage = $aiReading - $previousKwh;

        $rate = (float) (BusinessSettings::where('type', 'electric_meter_rate')->value('value') ?? 0);
        $total = $usage * $rate;

        $thirtyDayCap = (float) (BusinessSettings::where('type', 'electric_bill_high_threshold_30day')->value('value') ?? 150);
        $threshold = ($thirtyDayCap / 30.0) * $days;

        $draft = [
            'meter_number' => $aiMeterNumber,
            'kwhNo' => $aiReading,
            'image' => $path,
            'date' => now()->toDateString(),
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
            'previousKwh' => $previousKwh,
            'days' => $days,
            'usage' => $usage,
            'rate' => $rate,
            'total' => $total,
            'threshold' => $threshold,
            'siteid' => $site->siteid ?? null,
            'assign_siteid' => $request['assign_siteid'] ?? null,
        ];

        session(['electric_reading_draft' => $draft]);

        $reservation = null;
        $customer = null;
        if ($site) {
            $reservation = Reservation::where('siteid', $site->siteid)->latest()->first();
            $customer = $reservation ? User::find($reservation->customernumber) : null;
        }

        $isNewMeter = !$site;

        $reading = (object) [
            'id' => null,
            'kwhNo' => $draft['kwhNo'],
            'meter_number' => $draft['meter_number'],
            'ai_meter_number' => $draft['ai_meter_number'],
            'ai_meter_reading' => $draft['ai_meter_reading'],
            'ai_confidence' => $draft['ai_confidence'],
            'ai_notes' => $draft['ai_notes'],
            'ai_attempts' => $draft['ai_attempts'],
            'image' => $draft['image'],
            'date' => $draft['date'],
            'siteid' => $draft['siteid'],
            'usage' => $draft['usage'],
            'rate' => $draft['rate'],
            'total' => $draft['total'],
            'previousKwh' => $draft['previousKwh'],
            'new_meter_number' => $isNewMeter,
            'days' => $draft['days'],
            'threshold' => $draft['threshold'],
        ];

        return [
            'success' => true,
            'response' => view('meters.preview', [
                'reading' => $reading,
                'site' => $site,
                'customer' => $customer,
                'customer_name' => $customer ? trim($customer->f_name . ' ' . $customer->l_name) : null,
                'start_date' => \Carbon\Carbon::parse($previousDate)->toDateString(),
                'end_date' => now()->toDateString(),
                'reservation_id' => $reservation->id ?? '',
                'draft_token' => csrf_token(),
            ]),
        ];
    }

    public function saveReading(Request $r)
    {
        $isAjax = $r->ajax() || $r->wantsJson();

        $draft = session('electric_reading_draft');
        if (!$draft) {
            return $isAjax ? response()->json(['ok' => false, 'message' => 'Draft not found. Please rescan the meter photo.'], 400) : back()->with('error', 'Draft not found. Please rescan the meter photo.');
        }

        $r->validate([
            'meter_number' => 'required|string',
            'kwhNo' => 'required|numeric',
            'ai_success' => 'required',
            'ai_fixed' => 'nullable',
            'training_opt_in' => 'nullable|boolean',
            'assign_siteid' => 'nullable|string',
        ]);

        $meterNumber = preg_replace('/\D/', '', $r->meter_number);
        $draft['meter_number'] = $meterNumber;
        $draft['kwhNo'] = (float) $r->kwhNo;
        $draft['ai_success'] = filter_var($r->ai_success, FILTER_VALIDATE_BOOLEAN);
        $draft['ai_fixed'] = filter_var($r->ai_fixed ?? false, FILTER_VALIDATE_BOOLEAN);
        $draft['training_opt_in'] = filter_var($r->training_opt_in ?? false, FILTER_VALIDATE_BOOLEAN);

        $targetSiteId = $r->input('assign_siteid');

        if ($targetSiteId) {
            DB::transaction(function () use ($targetSiteId, $meterNumber) {
                $current = Site::where('meter_number', $meterNumber)->lockForUpdate()->first();
                $target = Site::where('siteid', $targetSiteId)->lockForUpdate()->first();

                if (!$target) {
                    throw new \RuntimeException('Target site not found.');
                }

                if ($current && $current->siteid !== $target->siteid) {
                    $current->update(['meter_number' => null]);
                    SystemLog::create([
                        'transaction_type' => 'Meter Reassigned',
                        'details' => json_encode([
                            'meter_number' => $meterNumber,
                            'from_site' => $current->siteid,
                            'to_site' => $target->siteid,
                        ]),
                    ]);
                }

                $target = Site::where('siteid', $targetSiteId)->first();

                if ($target) {
                    $target->update([
                        'meter_number' => $meterNumber,
                        'updated_at' => now(),
                    ]);
                }
            });
        }

        $readingRow = Readings::create([
            'meter_number' => $draft['meter_number'],
            'kwhNo' => $draft['kwhNo'],
            'image' => $draft['image'],
            'date' => $draft['date'],
            'ai_meter_number' => $draft['ai_meter_number'],
            'ai_meter_reading' => $draft['ai_meter_reading'],
            'ai_success' => $draft['ai_success'],
            'ai_fixed' => $draft['ai_fixed'],
            'manufacturer' => $draft['manufacturer'],
            'meter_style' => $draft['meter_style'],
            'ai_confidence' => $draft['ai_confidence'],
            'ai_notes' => $draft['ai_notes'],
            'ai_attempts' => $draft['ai_attempts'],
            'prompt_version' => $draft['prompt_version'],
            'model_version' => $draft['model_version'],
            'ai_latency_ms' => $draft['ai_latency_ms'],
        ]);

        $r->session()->forget('electric_reading_draft');

        if ($isAjax) {
            return response()->json([
                'ok' => true,
                'message' => 'Meter reading saved.',
                'redirect_url' => route('meters.index'),
                'reading_id' => $readingRow->id,
            ]);
        }
        return redirect()->route('meters.index')->with('success', 'Meter reading saved.');
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

    public function send(Request $r)
    {
        $isAjax = $r->ajax() || $r->wantsJson();

        $r->validate([
            'reading_id' => 'nullable|exists:electric_readings,id',
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

        $meter = preg_replace('/\D/', '', (string) $r->meter_number);

        $reading = null;

        if ($r->filled('reading_id')) {
            $reading = Readings::find($r->reading_id);
        }

        if (!$reading && $meter) {
            $reading = Readings::where('meter_number', $meter)->orderByDesc('date')->orderByDesc('id')->first();
        }

        if (!$reading) {
            $msg = 'Reading not found. Save the reading first, then send the bill.';
            return $r->ajax() || $r->wantsJson() ? response()->json(['ok' => false, 'message' => $msg], 404) : back()->with('error', $msg);
        }

        $reading->meter_number = preg_replace('/\D/', '', $r->meter_number);
        $reading->kwhNo = (float) $r->kwhNo;
        $reading->ai_success = filter_var($r->ai_success, FILTER_VALIDATE_BOOLEAN);
        $reading->ai_fixed = filter_var($r->ai_fixed ?? false, FILTER_VALIDATE_BOOLEAN);
        if (Schema::hasColumn($reading->getTable(), 'training_opt_in')) {
            $reading->training_opt_in = filter_var($r->training_opt_in ?? false, FILTER_VALIDATE_BOOLEAN);
        }
        $reading->save();

        if ($r->boolean('new_meter_number') === true) {
            $this->logAudit('Bill Validation', 'New meter detected: billing blocked', [
                'meter_number' => $reading->meter_number,
                'reading_id' => $reading->id,
            ]);
            $msg = 'New meter detected. You can Save, but cannot Send.';
            return $isAjax ? response()->json(['ok' => false, 'message' => $msg], 400) : back()->with('warning', $msg);
        }

        $usage = (float) $r->kwhNo - (float) ($r->prevkwhNo ?? 0);
        $total = $usage * (float) $r->rate;

        if ($total <= 0) {
            $this->logAudit('Bill Validation', 'Zero/negative bill blocked', [
                'reading_id' => $reading->id,
                'total' => $total,
            ]);
            $msg = 'Total is zero or negative. You can Save, but cannot Send.';
            return $isAjax ? response()->json(['ok' => false, 'message' => $msg], 400) : back()->with('error', $msg);
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
            $msg = 'Bill exceeds threshold. Tick "Send anyway?" to proceed.';
            return $isAjax ? response()->json(['ok' => false, 'message' => $msg], 409) : back()->with('warning', $msg);
        }

        $meter = $reading->meter_number;
        $contextSiteId = $r->siteid ?: null;

        if ($contextSiteId) {
            try {
                DB::transaction(function () use ($contextSiteId, $meter, $reading) {
                    $current = Site::where('meter_number', $meter)->lockForUpdate()->first();
                    $target = Site::where('siteid', $contextSiteId)->lockForUpdate()->first();

                    if (!$target) {
                        throw new \RuntimeException('Target site not found.');
                    }

                    if ($current && $current->siteid !== $target->siteid) {
                        $old = $current->siteid;
                        $current->update(['meter_number' => null]);
                        $target->update(['meter_number' => $meter]);

                        $this->logAudit('Meter Reassigned', 'Meter moved between sites', [
                            'meter_number' => $meter,
                            'from_site' => $old,
                            'to_site' => $target->siteid,
                            'reading_id' => $reading->id,
                        ]);
                    } elseif (!$current) {
                        $target->update(['meter_number' => $meter]);
                        $this->logAudit('Meter Reassigned', 'Meter assigned to site', [
                            'meter_number' => $meter,
                            'to_site' => $target->siteid,
                            'reading_id' => $reading->id,
                        ]);
                    }
                });
            } catch (\Throwable $e) {
                $msg = 'Site reassignment failed: ' . $e->getMessage();
                return $isAjax ? response()->json(['ok' => false, 'message' => $msg], 422) : back()->with('error', $msg);
            }
        } else {
            $currentAssigned = Site::where('meter_number', $meter)->first();
            if (!$currentAssigned) {
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

        // 7) Respond
        if ($isAjax) {
            return response()->json([
                'ok' => true,
                'message' => 'Bill saved and sent.',
                'redirect_url' => route('meters.index'),
                'bill_id' => $bill->id,
            ]);
        }
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
