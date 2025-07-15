<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

use App\Models\Readings;
use App\Models\Site;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Bills;
use App\Mail\ElectricBillGenerated;

class MeterController extends Controller
{
    public function index()
    {
        $thresholdDate = Carbon::now()->subDays(20)->toDateString();

        $overdueMeterNumbers = Readings::select('meter_number')
            ->groupBy('meter_number')
            ->havingRaw('MAX(date) <= ?', [$thresholdDate])
            ->pluck('meter_number');

        $overdueSites = Site::whereIn('meter_number', $overdueMeterNumbers)->get();

        return view('meters.index', compact('overdueSites'));
    }

    public function read(Request $request)
    {
        $path = $this->handleImageUpload($request);
        if (!$path) {
            return back()->with('Error', 'Image not found.');
        }

        [$meterNumber, $currentReading, $parsed, $imageUrl, $data, $content] = $this->parseImageViaGPT($path);

        if (!$meterNumber || !$currentReading) {
            session()->flash('retry_path', $path);
            return view('meters.gpt_debug', ['raw' => $content, 'response' => $data])
                ->with('error', 'Missing required values (meter number or reading).');
        }

        $site = Site::where('meter_number', $meterNumber)->first();
        if (!$site) {
            return redirect()->route('meters.unregistered', [
                'meter_number' => $meterNumber,
                'reading' => $currentReading,
                'image' => $path,
                'date' => now()->toDateString(),
            ]);
        }

        $lastReading = Readings::where('meter_number', $meterNumber)->latest('date')->first();
        if (!$lastReading) {
            return redirect()->route('meters.index')->with('info', 'First reading saved. No billing applied yet.');
        }

        return $this->buildPreviewView($lastReading, $currentReading, $meterNumber, $path, $site);
    }

    public function scan(Request $request)
    {
        $path = $this->handleImageUpload($request);
        if (!$path) {
            return back()->with('Error', 'Image not found.');
        }

        [$meterNumber, $currentReading, $parsed, $imageUrl, $data, $content] = $this->parseImageViaGPT($path);

        if (!$meterNumber || !$currentReading) {
            session()->flash('retry_path', $path);
            return view('meters.gpt_debug', ['raw' => $content, 'response' => $data])
                ->with('error', 'Missing required values (meter number or reading).');
        }

        $site = Site::where('meter_number', $meterNumber)->first();
        if (!$site) {
            return redirect()->route('meters.unregistered', [
                'meter_number' => $meterNumber,
                'reading' => $currentReading,
                'image' => $path,
                'date' => now()->toDateString(),
            ]);
        }

        $lastReading = Readings::where('meter_number', $meterNumber)->latest('date')->first();

        return $this->buildPreviewView($lastReading, $currentReading, $meterNumber, $path, $site);
    }

    public function unregister(Request $request)
    {
        return view('meters.unregistered', $request->only('meter_number', 'reading', 'image', 'date'));
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

        Site::where('meter_number', $newMeterNumber)
            ->where('siteid', '!=', $site->siteid)
            ->update(['meter_number' => null]);

        $site->meter_number = $newMeterNumber;
        $site->save();

        return redirect()->route('meters.preview.fromSession')->with('reading_data', $request->all());
    }

    public function previewFromSession()
    {
        $data = session('reading_data');

        if (!$data) {
            return redirect()->route('meters.index')->with('error', 'No reading data available.');
        }

        $site = Site::where('siteid', $data['siteid'])->first();
        $lastReading = Readings::where('meter_number', $data['meter_number'])->latest('date')->first();

        return $this->buildPreviewView($lastReading, $data['kwhNo'], $data['meter_number'], $data['image'], $site, true);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
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

        $rate = $validated['rate'] ?? 0.12;

        Readings::create([
            'kwhNo' => $validated['kwhNo'],
            'meter_number' => $validated['meter_number'],
            'image' => $validated['image'],
            'date' => now(),
        ]);

        if ($validated['customer_id'] && $request->boolean('new_meter_number')) {
            $bill = Bills::create([
                'reservation_id' => $validated['reservation_id'],
                'customer_id' => $validated['customer_id'],
                'kwh_used' => $validated['usage'],
                'rate' => $rate,
                'total_cost' => $validated['total'],
                'reading_dates' => json_encode([
                    'start' => $validated['start_date'],
                    'end' => $validated['end_date'],
                ]),
                'auto_email' => true,
            ]);

            $customer = User::find($validated['customer_id']);
            if ($customer && $customer->email) {
                Mail::to($customer->email)->send(new ElectricBillGenerated([
                    'customer' => $customer,
                    'site_no' => $validated['siteid'],
                    'current_reading' => $validated['kwhNo'],
                    'previous_reading' => $validated['prevkwhNo'],
                    'usage' => $validated['usage'],
                    'total' => $validated['total'],
                    'rate' => $rate,
                    'days' => $request->days,
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                ]));
            }
        }

        return redirect()->route('meters.index')->with('success', 'Bill saved and emailed.');
    }

    private function handleImageUpload(Request $request)
    {
        $request->validate(['photo' => 'nullable|image|max:5120']);

        if ($request->hasFile('photo')) {
            return Readings::storeFile($request->file('photo'));
        }

        return $request->filled('existing_image') ? $request->input('existing_image') : null;
    }

    private function parseImageViaGPT($path)
    {
        $imageUrl = asset('storage/' . $path);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'messages' => [[
                'role' => 'user',
                'content' => [[
                    'type' => 'text',
                    'text' => 'From this electric meter image, extract and return a JSON with the following fields: {"siteid":"<value>","meter_number":"<value>","reading":<value>} Always return your best guess, respond in JSON only.',
                ], [
                    'type' => 'image_url',
                    'image_url' => ['url' => $imageUrl],
                ]],
            ]],
            'max_tokens' => 100,
        ]);

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? null;

        if (!$content || !str_contains($content, '{')) {
            return [null, null, null, $imageUrl, $data, $content];
        }

        $start = strpos($content, '{');
        $end = strrpos($content, '}');
        $jsonString = substr($content, $start, $end - $start + 1);
        $parsed = json_decode($jsonString, true);

        $meterNumber = preg_replace('/\D/', '', trim($parsed['meter_number'] ?? ''));
        $currentReading = isset($parsed['reading']) ? (float) $parsed['reading'] : null;

        return [$meterNumber, $currentReading, $parsed, $imageUrl, $data, $content];
    }

    private function buildPreviewView($lastReading, $currentReading, $meterNumber, $path, $site, $isNew = false)
    {
        $previousKwh = $lastReading?->kwhNo ?? 0;
        $previousDate = $lastReading?->date ?? now();
        $usage = $currentReading - $previousKwh;
        $days = now()->diffInDays(Carbon::parse($previousDate));
        $rate = 0.12;
        $total = $usage * $rate;

        $reservation = Reservation::where('siteid', $site->siteid)
            ->whereDate('cid', '<=', now())
            ->whereDate('cod', '>=', now())
            ->first();

        $customer = $reservation ? User::find($reservation->customernumber) : null;

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
            'new_meter_number' => $isNew,
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
}