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

        $relativePath = Readings::storeFile($request->file('photo'));
        $imageUrl = asset('storage/' . $relativePath);

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
                            'text' => 'From this electric meter image, extract and return ONLY this JSON format: {"siteid": "A6", "meter_number": "47826745", "reading": 10946}. Do not include any explanation or units.',
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageUrl,
                            ],
                        ],
                    ],
                ],
            ],
            'max_tokens' => 100,
        ]);

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? null;

        if (!$content || !str_contains($content, '{')) {
            return view('meters.gpt_debug', ['raw' => $content, 'response' => $data]);
        }

        $start = strpos($content, '{');
        $end = strrpos($content, '}');
        $jsonString = substr($content, $start, $end - $start + 1);

        $parsed = json_decode($jsonString, true);

        if (!isset($parsed['siteid'], $parsed['reading'], $parsed['meter_number'])) {
            return back()->with('error', 'Invalid GPT response.');
        }

        $siteid = strtoupper(trim($parsed['siteid']));
        $meterNumber = trim($parsed['meter_number']);
        $currentReading = (float) $parsed['reading'];

        $lastReading = Readings::where('siteno', $siteid)->latest('date')->first();

        $previousReading = $lastReading?->bill ?? 0;
        $previousDate = $lastReading?->date ?? now();
        $usage = $currentReading - $previousReading;
        $days = now()->diffInDays($previousDate);
        $rate = 0.12;
        $total = $usage * $rate;

        $site = Site::where('siteid', $lastReading?->siteno)->first();

        $customer = null;
        $customerName = null;
        if ($site) {
            $latestReservation = Reservation::where('siteid', $site->siteid)->latest('created_at')->first();

            if ($latestReservation && $latestReservation->customernumber) {
                $customer = User::where('id', $latestReservation->customernumber)->first();
                $customerName = $customer ? $customer->f_name . ' ' . $customer->l_name : null;
            }
        }

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

        return view('meters.preview', [
            'reading' => $reading,
            'site' => $site,
            'customer_name' => $customerName,
            'customer_id' => $customer?->id,
            'customer' => $customer,
            'usage' => $usage,
            'rate' => $rate,
            'total' => $total,
            'days' => $days,
            'start_date' => $previousDate->toDateString(),
            'end_date' => now()->toDateString(),
        ]);
    }

    public function sendBill(Request $request)
    {
        $reading = Readings::findOrFail($request->reading_id);
        $customer = Customer::find($reading->customer_id);
        $site = Site::where('siteno', $reading->siteno)->first();

        $lastReading = Readings::where('kwhNo', $reading->kwhNo)->where('date', '<', $reading->date)->latest('date')->first();

        $usage = $reading->bill - ($lastReading?->bill ?? 0);
        $days = $reading->date->diffInDays($lastReading?->date ?? now());
        $rate = 0.12;
        $total = $usage * $rate;

        // Optionally update the reading record with bill
        $reading->update([
            'status' => 'billed',
            'bill' => $total,
        ]);

        // Send Email
        Mail::to($customer->email)->send(
            new \App\Mail\ElectricBillGenerated([
                'customer' => $customer,
                'site' => $site,
                'usage' => $usage,
                'total' => $total,
                'rate' => $rate,
                'days' => $days,
                'start_date' => optional($lastReading)->date?->toDateString(),
                'end_date' => $reading->date->toDateString(),
            ]),
        );

        return redirect()->route('meters.index')->with('success', 'Bill emailed to customer.');
    }
}
