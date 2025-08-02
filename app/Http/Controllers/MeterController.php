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

    public function read(Request $request)
    {
        $path = null;

        $request->validate([
            'photo' => 'required|image|max:5120',
        ]);

        // 1. Store file
        if ($request->hasFile('photo')) {
            $path = Readings::storeFile($request->file('photo'));
        } elseif ($request->filled('existing_image')) {
            $path = $request->input('existing_image');

            return back()->with('Warning', 'That Doesn`t seem right, try again.');
        }
        if (!$path || !file_exists(public_path('storage/' . $path))) {
            return back()->with('Error', 'Image not found.');
        }

        $imageUrl = asset('storage/' . $path);

        // 2. Send to CLAUDE AI
        $response = Http::withHeaders([
            'x-api-key' => env('CLAUDE_API_KEY'),
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 1024,
            'temperature' => 0,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $this->getClaudePromptMessage($imageUrl),
                        ],
                    ],
                ],
            ],
        ]);

        // 3. Parse CLAUDE AI result
        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? null;

        if (!$content || !str_contains($content, '{')) {
            session()->flash('retry_path', $path);
            return view('meters.gpt_debug', ['raw' => $content, 'response' => $data]);
        }

        $start = strpos($content, '{');
        $end = strrpos($content, '}');
        $jsonString = substr($content, $start, $end - $start + 1);
        $parsed = json_decode($jsonString, true);

        $meterNumber = preg_replace('/\D/', '', trim($parsed['meter_number'] ?? ''));
        $currentReading = isset($parsed['reading']) ? (float) $parsed['reading'] : null;

        if (!$meterNumber || !$currentReading) {
            return view('meters.gpt_debug', ['raw' => $content, 'response' => $data])->with('error', 'Missing required values (meter number or reading).');
        }

        // 4. Lookup site by meter_number
        $site = Site::where('meter_number', $meterNumber)->first();
        if (!$site) {
            return redirect()->route('meters.unregistered', [
                'meter_number' => $meterNumber,
                'reading' => $currentReading,
                'image' => $path,
                'date' => now()->toDateString(),
            ]);
        }

        // 5. Lookup last reading for this meter

        $lastReading = Readings::where('meter_number', $meterNumber)->latest('date')->first();
        $previousKwh = $lastReading?->kwhNo ?? 0;
        $previousDate = $lastReading?->date ?? now();
        $usage = $currentReading - $previousKwh;
        $days = now()->diffInDays(Carbon::parse($previousDate));
        $rate = BusinessSettings::where('type', 'electric_meter_rate')->value('value');
        $total = $usage * $rate;

        // 6. Find reservation for the reading date
        $reservation = Reservation::where('siteid', $site->siteid)->whereDate('cid', '<=', now())->whereDate('cod', '>=', now())->first();

        $customer = $reservation ? User::find($reservation->customernumber) : null;

        // 7. Build preview data
        $reading = (object) [
            'kwhNo' => $currentReading,
            'meter_number' => $meterNumber,
            'image' => $path,
            'date' => now()->toDateString(),
            'siteid' => $site->siteid,
            'usage' => $usage,
            'rate' => $rate,
            'total' => $total,
            'previousKwh' => $previousKwh,
            'new_meter_number' => false,
        ];

        // 8. Return to preview view
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

    public function scan(Request $request)
    {
        $path = null;

        $request->validate([
            'photo' => 'required|image|max:5120',
        ]);

        // 1. Store file
        if ($request->hasFile('photo')) {
            $path = Readings::storeFile($request->file('photo'));
        } elseif ($request->filled('existing_image')) {
            $path = $request->input('existing_image');

            return back()->with('Warning', 'That Doesn`t seem right, try again.');
        }
        if (!$path || !file_exists(public_path('storage/' . $path))) {
            return back()->with('Error', 'Image not found.');
        }

        $imageUrl = asset('storage/' . $path);

        // 2. Send to CLAUDE AI
        $response = Http::withHeaders([
            'x-api-key' => env('CLAUDE_API_KEY'),
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 1024,
            'temperature' => 0,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $this->getClaudePromptMessage($imageUrl),
                        ],
                    ],
                ],
            ],
        ]);

        // 3. Parse CLAUDE AI result
        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? null;

        if (!$content || !str_contains($content, '{')) {
            session()->flash('retry_path', $path);
            return view('meters.gpt_debug', ['raw' => $content, 'response' => $data]);
        }

        $start = strpos($content, '{');
        $end = strrpos($content, '}');
        $jsonString = substr($content, $start, $end - $start + 1);
        $parsed = json_decode($jsonString, true);

        $meterNumber = preg_replace('/\D/', '', trim($parsed['meter_number'] ?? ''));
        $currentReading = isset($parsed['reading']) ? (float) $parsed['reading'] : null;

        if (!$meterNumber || !$currentReading) {
            return view('meters.gpt_debug', ['raw' => $content, 'response' => $data])->with('error', 'Missing required values (meter number or reading).');
        }

        // 4. Lookup site by meter_number
        $site = Site::where('meter_number', $meterNumber)->first();
        if (!$site) {
            return redirect()->route('meters.unregistered', [
                'meter_number' => $meterNumber,
                'reading' => $currentReading,
                'image' => $path,
                'date' => now()->toDateString(),
            ]);
        }

        // 5. Lookup last reading for this meter

        $lastReading = Readings::where('meter_number', $meterNumber)->latest('date')->first();
        $previousKwh = $lastReading?->kwhNo ?? 0;
        $previousDate = $lastReading?->date ?? now();
        $usage = $currentReading - $previousKwh;
        $days = now()->diffInDays(Carbon::parse($previousDate));
        $rate = BusinessSettings::where('type', 'electric_meter_rate')->value('value');
        $total = $usage * $rate;

        // 6. Find reservation for the reading date
        $reservation = Reservation::where('siteid', $site->siteid)->whereDate('cid', '<=', now())->whereDate('cod', '>=', now())->first();

        $customer = $reservation ? User::find($reservation->customernumber) : null;

        // 7. Build preview data
        $reading = (object) [
            'kwhNo' => $currentReading,
            'meter_number' => $meterNumber,
            'image' => $path,
            'date' => now()->toDateString(),
            'siteid' => $site->siteid,
            'usage' => $usage,
            'rate' => $rate,
            'total' => $total,
            'previousKwh' => $previousKwh,
            'new_meter_number' => false,
        ];

        // 8. Return to preview view
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
    private function getClaudePromptMessage(string $type = 'meter_page', string $imageUrl = ''): string
    {
        $template = DB::table('prompt_templates')->where('type', $type)->select('user_prompt')->first();

        $userPrompt =
            $template->user_prompt ??
            <<<DEFAULT
            From this electric meter image ($imageUrl), extract and return a JSON with the following fields:

            {
              "siteid": "<value from large sticker label usually in black or white>",
              "meter_number": "<printed or stamped number near the bottom of the meter, e.g., 46193471>",
              "reading": <the large numeric display at the top of the meter>
            }

            Always return your best guess, even if some values are unclear. Respond only with valid JSON and no explanation.
            DEFAULT;

        if (!str_contains($userPrompt, $imageUrl)) {
            $userPrompt .= "\n\nImage: $imageUrl";
        }

        return $userPrompt;
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
            'meter_number' => 'required|string',
            'image' => 'required|string',
            'kwhNo' => 'required|numeric',
            'prevkwhNo' => 'required|numeric',
            'total' => 'required|numeric',
            'siteid' => 'required|string',
            'usage' => 'required|string',
            'reservation_id' => 'nullable|exists:reservations,id',
            'customer_id' => 'nullable|exists:users,id',
            'rate' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $rate = $request->rate ?? BusinessSettings::where('type', 'electric_meter_rate')->value('value');
        $readingDate = Carbon::now();

        // CASE 1: No customer — update existing reading
        if (!$request->filled('customer_id')) {
            $latestReading = Readings::where('meter_number', $request->meter_number)->latest('date')->first();

            if ($latestReading) {
                $latestReading->update([
                    'kwhNo' => $request->kwhNo,
                    'image' => $request->image,
                    'date' => $readingDate,
                ]);
            }

            if ($latestReading->kwhNo >= $request->kwhNo) {
                return redirect()->route('meters.index')->with('warning', 'New reading must be greater than the last reading.');
            }

            return redirect()->route('meters.index')->with('info', 'Reading updated (no customer found).');
        }

        // CASE 2: Customer exists — save reading + bill + send mail
        $reading = Readings::create([
            'kwhNo' => $request->kwhNo,
            'meter_number' => $request->meter_number,
            'image' => $request->image,
            'date' => $readingDate,
        ]);

        if ($request->filled('customer_id')) {
            Bills::create([
                'reservation_id' => $request->reservation_id,
                'customer_id' => $request->customer_id,
                'kwh_used' => $request->usage,
                'rate' => $rate,
                'total_cost' => $request->total,
                'reading_dates' => json_encode([
                    'start' => $request->start_date,
                    'end' => $request->end_date,
                ]),
                'auto_email' => true,
            ]);

            $customer = User::find($request->customer_id);
            if ($customer && $customer->email) {
                Mail::to($customer->email)->send(
                    new ElectricBillGenerated([
                        'customer' => $customer,
                        'site_no' => $request->siteid,
                        'current_reading' => $request->kwhNo,
                        'previous_reading' => $request->prevkwhNo,
                        'usage' => $request->usage,
                        'total' => $request->total,
                        'rate' => $rate,
                        'days' => $request->days,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                    ]),
                );
            }
        }

        return redirect()->route('meters.index')->with('success', 'Bill saved and emailed.');
    }
}
