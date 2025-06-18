<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

use App\Models\Readings;
use App\Models\Site;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Bills;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

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

        $overdueSites = Site::whereIn('neter_number', $overDueMeterNumbers)->get();
        return view('meters.index', compact('overdueSites'));
    }

    public function read(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120',
        ]);

        // 1. Store file
        $relativePath = Readings::storeFile($request->file('photo'));
        $imageUrl = asset('storage/' . $relativePath);

        // 2. Send to GPT
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'From this electric meter image, extract and return a JSON with the following fields:
                            {
                            "siteid": "<value from large sticker label usually in black or white>",
                            "meter_number": "<printed or stamped number near the bottom of the meter, e.g., 46193471>",
                            "reading": <the large numeric display at the top of the meter>
                            }
                            Always return your best guess, even if some values are unclear. Respond in JSON format only without explanation.',
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => ['url' => $imageUrl],
                        ],
                    ],
                ],
            ],
            'max_tokens' => 100,
        ]);

        // 3. Parse GPT result
        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? null;

        if (!$content || !str_contains($content, '{')) {
            return view('meters.gpt_debug', ['raw' => $content, 'response' => $data]);
        }

        $start = strpos($content, '{');
        $end = strrpos($content, '}');
        $jsonString = substr($content, $start, $end - $start + 1);
        $parsed = json_decode($jsonString, true);

        $meterNumber = trim($parsed['meter_number'] ?? '');
        $currentReading = isset($parsed['reading']) ? (float) $parsed['reading'] : null;

        if (!$meterNumber || !$currentReading) {
            return view('meters.gpt_debug', ['raw' => $content, 'response' => $data])->with('error', 'Missing required values (meter number or reading).');
        }

        // 4. Lookup site by meter_number
        $site = Site::where('meter_number', $meterNumber)->first();
        if (!$site) {
            return view('meters.unregistered', [
                'meter_number' => $meterNumber,
                'reading' => $currentReading,
                'image' => $relativePath,
                'date' => now()->toDateString(),
            ]);
        }

        // 5. Lookup last reading for this meter

        $lastReading = Readings::where('meter_number', $meterNumber)->latest('date')->first();
        $previousKwh = $lastReading?->kwhNo ?? 0;
        $previousDate = $lastReading?->date ?? now();
        $usage = $currentReading - $previousKwh;
        $days = now()->diffInDays(Carbon::parse($previousDate));
        $rate = 0.12;
        $total = $usage * $rate;

        // 6. Find reservation for the reading date
        $reservation = Reservation::where('siteid', $site->siteid)->whereDate('cid', '<=', now())->whereDate('cod', '>=', now())->first();

        $customer = $reservation ? User::find($reservation->customernumber) : null;

        // 7. Build preview data
        $reading = (object) [
            'kwhNo' => $currentReading,
            'meter_number' => $meterNumber,
            'image' => $relativePath,
            'date' => now()->toDateString(),
            'siteid' => $site->siteid,
            'usage' => $usage,
            'rate' => $rate,
            'total' => $total,
            'previousKwh' => $previousKwh,
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
            'reservation_id' => $reservation->id,
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
        $rate = 0.12;
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
        ];

        return view('meters.preview', [
            'reading' => $reading,
            'site' => $site,
            'customer' => $customer,
            'customer_name' => $customer ? trim($customer->f_name . ' ' . $customer->l_name) : null,
            'start_date' => Carbon::parse($previousDate)->toDateString(),
            'end_date' => now()->toDateString(),
            'days' => $days,
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

        $rate = $request->rate ?? 0.12;
        $readingDate = Carbon::now();

    

        // 1. Create reading record
        $reading = Readings::create([
            'kwhNo' => $request->kwhNo,
            'meter_number' => $request->meter_number,
            'image' => $request->image,
            'date' => $readingDate,
        ]);

        // 2. Create bill
        $bill = Bill::create([
            'reservation_id' => $request->reservation_id,
            'customer_id' => $request->customer_id,
            'kwh_used' => $request->usage,
            'rate' => $rate,
            'total_cost' => $request->total,
            'reading_bills' => json_encode([
                'start' => $request->start_date,
                'end' => $request->end_date,
            ]),
            'auto_email' => true,
        ]);

        // 3. Send email
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

        return redirect()->route('meters.index')->with('success', 'Bill saved and emailed.');
    }
}
