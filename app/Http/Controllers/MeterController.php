<?php

namespace App\Http\Controllers;

use App\Models\Readings;
use App\Models\Site;
use App\Models\Customer;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

use App\Mail\ElectricBillGenerated;
class MeterController extends Controller
{
    public function index()
    {
        $overdueSites = Readings::select('siteno')->groupBy('siteno')->havingRaw('MAX(date) <= CURDATE() - INTERVAL 20 DAY')->get();

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

        // 4. Validate extracted fields
        $siteid = $parsed['siteid'] ?? '';
        $meterNumber = trim($parsed['meter_number'] ?? '');
        $currentReading = isset($parsed['reading']) ? (float) $parsed['reading'] : null;

        if (!$siteid || !$meterNumber || !$currentReading) {
            return view('meters.gpt_debug', ['raw' => $content, 'response' => $data])->with('error', 'Missing required values.');
        }

        // 5. Get previous reading
        $lastReading = Readings::where('siteno', $siteid)->latest('date')->first();
        $previousReading = $lastReading?->bill ?? 0;
        $previousDate = $lastReading?->date ?? now();
        $usage = $currentReading - $previousReading;
        $days = now()->diffInDays($previousDate);
        $rate = 0.12;
        $total = $usage * $rate;

        // 6. Find site and customer
        $site = Site::where('siteid', $siteid)->first();
        $customer = null;
        $customerName = null;

        if ($site) {
            $latestReservation = Reservation::where('siteid', $site->siteid)->latest('created_at')->first();
            if ($latestReservation && $latestReservation->customernumber) {
                $customer = User::find($latestReservation->customernumber);
                $customerName = $customer ? trim($customer->f_name . ' ' . $customer->l_name) : null;
            }
        }

        // 7. Preview object for Blade
        $reading = (object) [
            'kwhNo' => $currentReading,
            'meter_number' => $meterNumber,
            'image' => $relativePath,
            'date' => now(),
            'siteno' => $siteid,
            'status' => 'pending',
            'bill' => $total,
            'customer_id' => $customer?->id,
            'customer_name' => $customerName,
        ];

        // 8. Return to preview view
        return view('meters.preview', [
            'meter_number' => $meterNumber,
            'image' => $relativePath,
            'reading' => $reading,
            'site' => $site,
            'siteid' => $siteid,
            'customer_name' => $customerName,
            'customer_id' => $customer?->id,
            'customer' => $customer,
            'usage' => $usage,
            'rate' => $rate,
            'total' => $total,
            'days' => $days,
            'start_date' => Carbon::parse($previousDate)->toDateString(),
            'end_date' => Carbon::parse(now())->toDateString(),
        ]);
    }

    public function send(Request $request)
    {
        $reading = Readings::create([
            'kwhNo' => $request->kwhNo,
            'meter_number' => $request->meter_number,
            'image' => $request->image,
            'date' => now(),
            'siteno' => $request->siteno,
            'status' => 'billed',
            'bill' => $request->bill,
            'customer_id' => $request->customer_id,
        ]);

        $customer = User::find($reading->customer_id);
        $site = Site::where('siteid', $reading->siteno)->first();

        $lastReading = Readings::where('siteno', $reading->siteno)->where('date', '<', $reading->date)->latest('date')->first();

        $previousReading = $lastReading?->kwhNo ?? 0;
        $usage = $reading->kwhNo - $previousReading;
        $days = now()->diffInDays($lastReading?->date ?? now());
        $rate = 0.12;
        $total = $usage * $rate;

        $reading->update(['bill' => $total]);

        Mail::to($customer->email)->send(
            new ElectricBillGenerated([
                'customer' => $customer,
                'site_no' => $reading->siteno,
                'current_reading' => $reading->kwhNo,
                'previous_reading' => $previousReading,
                'usage' => $usage,
                'total' => $total,
                'rate' => $rate,
                'days' => $days,
                'start_date' => optional($lastReading)->date ? Carbon::parse($lastReading->date)->toDateString() : null,
                'end_date' => now()->toDateString(),
            ]),
        );

        return redirect()->route('meters.index')->with('success', 'Bill saved and emailed.');
    }
}
