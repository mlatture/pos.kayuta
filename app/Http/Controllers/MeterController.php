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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\ElectricBillGenerated;

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
        return view('meters.index', compact('overdueSites'));
    }

    private function getClaudePromptMessage(string $type = 'meter_page', string $imageUrl = ''): string
    {
        $template = DB::table('prompt_templates')->where('type', $type)->select('user_prompt')->first();

        $userPrompt = trim($template->user_prompt ?? '');

        $strict = <<<EOT

        Return ONLY this JSON exactly, no prose:

        {
          "meter_number": "<digits only>",
          "reading": "<numeric reading (kWh)>",
          "manufacturer": "<string or empty>",
          "meter_style": "<string or empty>"
        }
        EOT;

        // Never duplicate the image URL; add if absent.
        if ($imageUrl && !str_contains($userPrompt, $imageUrl)) {
            $userPrompt .= "\n\nImage: {$imageUrl}";
        }

        return $userPrompt . "\n\n" . $strict;
    }

  
    public function read(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120',
        ]);

        // 1) Store file
        if ($request->hasFile('photo')) {
            $path = Readings::storeFile($request->file('photo')); // update helper to static on Readings
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


        // 2) Build prompt
        $imageUrl = asset('storage/' . $path);
        $textPrompt = $this->getClaudePromptMessage('meter_page', $imageUrl);

        $payload = [
            'model' => 'claude-3-5-sonnet-20250514', // keep/to your latest known
            'max_tokens' => 512,
            'temperature' => 0,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $textPrompt],
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => 'image/jpeg', // or detect from $request->file('photo')->getMimeType()
                                'data' => $base64,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $t0 = microtime(true);
        $response = Http::withHeaders([
            'x-api-key' => env('CLAUDE_API_KEY'),
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', $payload);
        $latencyMs = (int) ((microtime(true) - $t0) * 1000);

        if (!$response->ok()) {
            Log::error('Claude API error', ['status' => $response->status(), 'body' => $response->body()]);
            return back()->with('error', 'Scan failed. Please retake a clearer photo.');
        }

        $data = $response->json();
        $contentBlocks = $data['content'] ?? [];
        $rawText = '';
        foreach ($contentBlocks as $blk) {
            if (($blk['type'] ?? '') === 'text') {
                $rawText .= $blk['text'] . "\n";
            }
        }

        // 3) Robust JSON extraction
        if (!Str::contains($rawText, '{')) {
            session()->flash('retry_path', $path);
            return view('meters.gpt_debug', ['raw' => $rawText, 'response' => $data])->with('warning', 'Model did not return JSON. Please review.');
        }

        // first top-level JSON object
        if (!preg_match('/\{.*\}/s', $rawText, $m)) {
            session()->flash('retry_path', $path);
            return view('meters.gpt_debug', ['raw' => $rawText, 'response' => $data])->with('warning', 'Unparseable response. Please review.');
        }

        $parsed = json_decode($m[0], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            session()->flash('retry_path', $path);
            return view('meters.gpt_debug', ['raw' => $rawText, 'response' => $data])->with('warning', 'Invalid JSON returned by AI.');
        }

        $aiMeterNumber = preg_replace('/\D/', '', trim($parsed['meter_number'] ?? ''));
        $aiReading = isset($parsed['reading']) ? (float) $parsed['reading'] : null;
        $manufacturer = trim($parsed['manufacturer'] ?? '');
        $meterStyle = trim($parsed['meter_style'] ?? '');

        if (!$aiMeterNumber || $aiReading === null) {
            return view('meters.gpt_debug', ['raw' => $rawText, 'response' => $data])->with('error', 'Missing required values (meter_number/reading).');
        }

        // 4) New/Existing site detection
        $site = Site::where('meter_number', $aiMeterNumber)->first();

        // 5) Persist an initial reading row (ai_* only). Admin will edit on preview.
        $readingRow = Readings::create([
            'meter_number' => $aiMeterNumber, // prefill user-facing fields from AI
            'kwhNo' => $aiReading, // ditto
            'image' => $path,
            'date' => now()->toDateString(),
            'siteid' => $site->siteid ?? null,

            'ai_meter_number' => $aiMeterNumber,
            'ai_meter_reading' => $aiReading,
            'ai_success' => true, // default true; UI change will flip to false
            'ai_fixed' => false,

            'manufacturer' => $manufacturer ?: null,
            'meter_style' => $meterStyle ?: null,

            'prompt_version' => 'meter_page/v1', // optional, or from prompt_templates
            'model_version' => (string) ($payload['model'] ?? ''),
            'ai_latency_ms' => $latencyMs,
        ]);

        // 6) Compute billing metrics (but DO NOT allow send if new meter)
        $last = Readings::where('meter_number', $aiMeterNumber)->where('id', '<', $readingRow->id)->latest('date')->first();

        $previousKwh = $last?->kwhNo ?? 0;
        $previousDate = $last?->date ?? now();
        $days = max(1, now()->diffInDays(Carbon::parse($previousDate)));
        $usage = $aiReading - $previousKwh;

        $rate = (float) (BusinessSettings::where('type', 'electric_meter_rate')->value('value') ?? 0);
        $total = $usage * $rate;

        // Threshold (per spec): daily rate threshold from business_settings
        $thirtyDayCap = (float) (BusinessSettings::where('type', 'electric_bill_high_threshold_30day')->value('value') ?? 150);
        $threshold = ($thirtyDayCap / 30.0) * $days;

        $reservation = null;
        $customer = null;
        if ($site) {
            $reservation = Reservation::where('siteid', $site->siteid)->whereDate('cid', '<=', now())->whereDate('cod', '>=', now())->first();
            $customer = $reservation ? User::find($reservation->customernumber) : null;
        }

        // New meter rule: if no site attached, treat as unregistered/new meter
        $isNewMeter = !$site;

        // 7) Build preview DTO (object) for view
        $reading = (object) [
            'id' => $readingRow->id,
            'kwhNo' => $readingRow->kwhNo,
            'meter_number' => $readingRow->meter_number,
            'ai_meter_number' => $readingRow->ai_meter_number,
            'ai_meter_reading' => $readingRow->ai_meter_reading,
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

        return view('meters.preview', [
            'reading' => $reading,
            'site' => $site,
            'customer' => $customer,
            'customer_name' => $customer ? trim($customer->f_name . ' ' . $customer->l_name) : null,
            'start_date' => Carbon::parse($previousDate)->toDateString(),
            'end_date' => now()->toDateString(),
            'reservation_id' => $reservation->id ?? '',
        ]);
    }

     public function scan(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120',
        ]);

        // 1) Store file
        if ($request->hasFile('photo')) {
            $path = Readings::storeFile($request->file('photo')); // update helper to static on Readings
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

        // 2) Build prompt
        $imageUrl = asset('storage/' . $path);
        $textPrompt = $this->getClaudePromptMessage('meter_page', $imageUrl);

        $payload = [
            'model' => 'claude-3-5-sonnet-20250514', // keep/to your latest known
            'max_tokens' => 512,
            'temperature' => 0,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $textPrompt],
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => 'image/jpeg', // or detect from $request->file('photo')->getMimeType()
                                'data' => $base64,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $t0 = microtime(true);
        $response = Http::withHeaders([
            'x-api-key' => env('CLAUDE_API_KEY'),
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', $payload);
        $latencyMs = (int) ((microtime(true) - $t0) * 1000);

        if (!$response->ok()) {
            Log::error('Claude API error', ['status' => $response->status(), 'body' => $response->body()]);
            return back()->with('error', 'Scan failed. Please retake a clearer photo.');
        }

        $data = $response->json();
        $contentBlocks = $data['content'] ?? [];
        $rawText = '';
        foreach ($contentBlocks as $blk) {
            if (($blk['type'] ?? '') === 'text') {
                $rawText .= $blk['text'] . "\n";
            }
        }

        // 3) Robust JSON extraction
        if (!Str::contains($rawText, '{')) {
            session()->flash('retry_path', $path);
            return view('meters.gpt_debug', ['raw' => $rawText, 'response' => $data])->with('warning', 'Model did not return JSON. Please review.');
        }

        // first top-level JSON object
        if (!preg_match('/\{.*\}/s', $rawText, $m)) {
            session()->flash('retry_path', $path);
            return view('meters.gpt_debug', ['raw' => $rawText, 'response' => $data])->with('warning', 'Unparseable response. Please review.');
        }

        $parsed = json_decode($m[0], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            session()->flash('retry_path', $path);
            return view('meters.gpt_debug', ['raw' => $rawText, 'response' => $data])->with('warning', 'Invalid JSON returned by AI.');
        }

        $aiMeterNumber = preg_replace('/\D/', '', trim($parsed['meter_number'] ?? ''));
        $aiReading = isset($parsed['reading']) ? (float) $parsed['reading'] : null;
        $manufacturer = trim($parsed['manufacturer'] ?? '');
        $meterStyle = trim($parsed['meter_style'] ?? '');

        if (!$aiMeterNumber || $aiReading === null) {
            return view('meters.gpt_debug', ['raw' => $rawText, 'response' => $data])->with('error', 'Missing required values (meter_number/reading).');
        }

        // 4) New/Existing site detection
        $site = Site::where('meter_number', $aiMeterNumber)->first();

        // 5) Persist an initial reading row (ai_* only). Admin will edit on preview.
        $readingRow = Readings::create([
            'meter_number' => $aiMeterNumber, // prefill user-facing fields from AI
            'kwhNo' => $aiReading, // ditto
            'image' => $path,
            'date' => now()->toDateString(),
            'siteid' => $site->siteid ?? null,

            'ai_meter_number' => $aiMeterNumber,
            'ai_meter_reading' => $aiReading,
            'ai_success' => true, // default true; UI change will flip to false
            'ai_fixed' => false,

            'manufacturer' => $manufacturer ?: null,
            'meter_style' => $meterStyle ?: null,

            'prompt_version' => 'meter_page/v1', // optional, or from prompt_templates
            'model_version' => (string) ($payload['model'] ?? ''),
            'ai_latency_ms' => $latencyMs,
        ]);

        // 6) Compute billing metrics (but DO NOT allow send if new meter)
        $last = Readings::where('meter_number', $aiMeterNumber)->where('id', '<', $readingRow->id)->latest('date')->first();

        $previousKwh = $last?->kwhNo ?? 0;
        $previousDate = $last?->date ?? now();
        $days = max(1, now()->diffInDays(Carbon::parse($previousDate)));
        $usage = $aiReading - $previousKwh;

        $rate = (float) (BusinessSettings::where('type', 'electric_meter_rate')->value('value') ?? 0);
        $total = $usage * $rate;

        // Threshold (per spec): daily rate threshold from business_settings
        $thirtyDayCap = (float) (BusinessSettings::where('type', 'electric_bill_high_threshold_30day')->value('value') ?? 150);
        $threshold = ($thirtyDayCap / 30.0) * $days;

        $reservation = null;
        $customer = null;
        if ($site) {
            $reservation = Reservation::where('siteid', $site->siteid)->whereDate('cid', '<=', now())->whereDate('cod', '>=', now())->first();
            $customer = $reservation ? User::find($reservation->customernumber) : null;
        }

        // New meter rule: if no site attached, treat as unregistered/new meter
        $isNewMeter = !$site;

        // 7) Build preview DTO (object) for view
        $reading = (object) [
            'id' => $readingRow->id,
            'kwhNo' => $readingRow->kwhNo,
            'meter_number' => $readingRow->meter_number,
            'ai_meter_number' => $readingRow->ai_meter_number,
            'ai_meter_reading' => $readingRow->ai_meter_reading,
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

        return view('meters.preview', [
            'reading' => $reading,
            'site' => $site,
            'customer' => $customer,
            'customer_name' => $customer ? trim($customer->f_name . ' ' . $customer->l_name) : null,
            'start_date' => Carbon::parse($previousDate)->toDateString(),
            'end_date' => now()->toDateString(),
            'reservation_id' => $reservation->id ?? '',
        ]);
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

    public function send(Request $request)
    {
        $request->validate([
            'reading_id' => 'nullable|integer|exists:electric_readings,id',
            'meter_number' => 'required|string',
            'image' => 'required|string',
            'kwhNo' => 'required|numeric',
            'prevkwhNo' => 'required|numeric',
            'siteid' => 'nullable|string',
            'reservation_id' => 'nullable|exists:reservations,id',
            'customer_id' => 'nullable|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'ai_success' => 'nullable',
            'training_opt_in' => 'nullable|boolean',
            'override_send' => 'nullable|boolean',
        ]);

        // Normalize inputs
        $meterNumber = preg_replace('/\D/', '', (string) $request->meter_number);
        $currentKwh = (float) $request->kwhNo;
        $prevKwh = (float) $request->prevkwhNo;
        $customerId = $request->input('customer_id');
        $reservationId = $request->input('reservation_id');
        $image = $request->image;
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $days = max(1, $start->diffInDays($end) + 1);

        $lastReading = Readings::where('meter_number', $meterNumber)->latest('date')->first();
        $serverPrevKwh = $lastReading?->kwhNo ?? 0.0;
        $serverPrevDate = $lastReading?->date ? Carbon::parse($lastReading->date) : $start->copy()->subDay();
        $prevKwh = max($prevKwh, $serverPrevKwh);

        $usage = $currentKwh - $prevKwh;

        // Monotonic check
        if ($usage <= 0) {
            return back()->with('error', 'New reading must be greater than the last reading.');
        }

        $rate = (float) (BusinessSettings::where('type', 'electric_meter_rate')->value('value') ?? 0);
        $total = $usage * $rate;

        $thirtyDayCap = (float) (BusinessSettings::where('type', 'electric_bill_high_threshold_30day')->value('value') ?? 150);
        $threshold = ($thirtyDayCap / 30.0) * $days;

        $site = Site::where('meter_number', $meterNumber)->first();
        $isNewMeter = !$site;

        if ($total <= 0) {
            return back()->with('error', 'Zero/negative bill cannot be sent. Please verify the rate in Business Settings.');
        }

        if ($total > $threshold && !$request->boolean('override_send')) {
            $this->systemLog('Bill Validation', [
                'meter_number' => $meterNumber,
                'reading_id' => $request->input('reading_id'),
                'total' => $total,
                'threshold' => $threshold,
                'days' => $days,
                'action' => 'blocked_without_override',
            ]);
            return back()->with('warning', 'Bill exceeds the threshold. Tick "Send anyway?" to proceed if legitimate.');
        }

        if ($isNewMeter || !$customerId) {
            $reading = $request->filled('reading_id') ? Readings::findOrFail($request->reading_id) : new Readings();

            $reading->fill([
                'meter_number' => $meterNumber,
                'kwhNo' => $currentKwh,
                'image' => $image,
                'date' => Carbon::now()->toDateString(),
                'siteid' => $site?->siteid, // null if new meter
            ]);

            if ($request->has('ai_success') && $request->input('ai_success') === 'false') {
                $reading->ai_success = false;
                $reading->ai_fixed = true;
                if ($request->boolean('training_opt_in')) {
                    $this->systemLog('AI Training', [
                        'reading_id' => $reading->id,
                        'meter_number' => $meterNumber,
                        'image' => $image,
                    ]);
                }
            }

            $reading->save();

            $msg = $isNewMeter ? 'Reading saved. New meter detected — guest billing disabled.' : 'Reading saved (no customer found).';

            return redirect()->route('meters.index')->with('info', $msg);
        }

        try {
            DB::transaction(function () use ($request, $meterNumber, $currentKwh, $image, $site, $customerId, $reservationId, $usage, $rate, $total, $start, $end, $days, $threshold) {
                if ($request->filled('siteid') && $site && $site->siteid != $request->siteid) {
                    $oldSite = Site::where('siteid', $request->siteid)->first();
                    if ($oldSite && $oldSite->meter_number === $meterNumber) {
                        $oldSite->meter_number = null;
                        $oldSite->save();
                    }
                    $this->systemLog('Meter Reassigned', [
                        'from_siteid' => $oldSite?->siteid,
                        'to_siteid' => $site->siteid,
                        'meter_number' => $meterNumber,
                    ]);
                }

                $reading = $request->filled('reading_id') ? Readings::findOrFail($request->reading_id) : new Readings();

                $reading->fill([
                    'meter_number' => $meterNumber,
                    'kwhNo' => $currentKwh,
                    'image' => $image,
                    'date' => Carbon::now()->toDateString(),
                    'siteid' => $site?->siteid,
                ]);

                if ($request->has('ai_success') && $request->input('ai_success') === 'false') {
                    $reading->ai_success = false;
                    $reading->ai_fixed = true;
                    if ($request->boolean('training_opt_in')) {
                        $this->systemLog('AI Training', [
                            'reading_id' => $reading->id,
                            'meter_number' => $meterNumber,
                            'image' => $image,
                        ]);
                    }
                }
                $reading->save();

                Bills::create([
                    'reservation_id' => $reservationId,
                    'customer_id' => $customerId,
                    'kwh_used' => $usage,
                    'rate' => $rate,
                    'total_cost' => $total,
                    'reading_dates' => json_encode([
                        'start' => $start->toDateString(),
                        'end' => $end->toDateString(),
                    ]),
                    'auto_email' => true,
                ]);

                if ($total > $threshold && $request->boolean('override_send')) {
                    $this->systemLog('Bill Validation', [
                        'reading_id' => $reading->id,
                        'total' => $total,
                        'threshold' => $threshold,
                        'days' => $days,
                        'action' => 'override_send',
                    ]);
                }

                // Email customer
                $customer = User::find($customerId);
                if ($customer && $customer->email) {
                    Mail::to($customer->email)->send(
                        new ElectricBillGenerated([
                            'customer' => $customer,
                            'site_no' => $site?->siteid,
                            'current_reading' => $currentKwh,
                            'previous_reading' => $request->prevkwhNo, // display value
                            'usage' => $usage,
                            'total' => $total,
                            'rate' => $rate,
                            'days' => $days,
                            'start_date' => $start->toDateString(),
                            'end_date' => $end->toDateString(),
                        ]),
                    );
                }
            });
        } catch (\Throwable $e) {
            Log::error('Send meter bill failed', ['err' => $e->getMessage()]);
            return back()->with('error', 'Failed to save and send bill.');
        }

        return redirect()->route('meters.index')->with('success', 'Bill saved and emailed.');
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
