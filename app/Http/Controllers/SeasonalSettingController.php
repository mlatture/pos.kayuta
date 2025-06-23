<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\RateTier;
use App\Models\SeasonalSetting;
use App\Models\SeasonalRenewal;
use App\Models\User;

use App\Notifications\SeasonalRenewalLinkNotification;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;


class SeasonalSettingController extends Controller
{
    //

    public function index()
    {
        $setting = session('success') ? null : SeasonalSetting::latest()->first();

        $rateTiers = RateTier::distinct('tier')->pluck('tier')->toArray();
        return view('admin.seasonal.index', compact('setting', 'rateTiers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'default_rate' => 'nullable|numeric',
            'discount_percentage' => 'nullable|numeric',
            'renewal_deadline' => 'nullable|date',
            'deposit_amount' => 'nullable|numeric',
            'rate_tiers' => 'nullable|array',
        ]);

        $data['rate_tiers'] = $request->rate_tiers;
        SeasonalSetting::create($data);

        return redirect()->back()->with('success', 'Seasonal settings saved.');
    }

    public function triggerRenewals()
    {
        $setting = SeasonalSetting::latest()->first();

        if (!$setting) {
            return back()->with('error', 'No seasonal settings found.');
        }

        $users = User::where('seasonal', true)
            ->with(['latestReservation.siteForSeasonal'])
            ->get();
        $count = 0;

        foreach ($users as $user) {
            $tier = $user->latestReservation->siteForSeasonal->ratetier ?? null;
            $rate = $setting->rate_tiers[$tier] ?? $setting->default_rate;

            SeasonalRenewal::updateOrCreate(
                ['customer_id' => $user->id],
                [
                    'offered_rate' => $rate,
                    'status' => 'pending',
                    'renewed' => false,
                    'response_date' => null,
                    'notes' => null,
                ],
            );

            URL::forceRootUrl('https://book.kayuta.com');
            // URL::forceRootUrl('http://127.0.0.1:8001');
            $signedUrl = URL::temporarySignedRoute('seasonal.renewal.guest', now()->addDays(14), ['user' => $user->id]);

            //Generate contract
            $templatePath = public_path('storages/templates/contract_template.docx');
            $fileName = "contract_{$user->l_name}_{$user->id}.docx";
            $filePath = public_path("storages/contracts/$fileName");

            $siteNumber = $user->latestReservation->siteForSeasonal->siteid ?? 'N/A';

            $templateProcessor = new TemplateProcessor($templatePath);
            $templateProcessor->setValues([
                'first_name' => $user->f_name,
                'last_name' => $user->l_name,
                'site_number' => $siteNumber,
                'seasonal_rate' => "$" . number_format($rate),
                'deadline' => $setting->renewal_deadline->format('F j, Y'),
                'discount_amount' => '$100',
                'year' => now()->year + 1,
            ]);
            $templateProcessor->saveAs($filePath);

            // Generate downloadable link 
            // $downloadLink = route('contracts.download', ['user' => $user->id]);

            $user->notify(new SeasonalRenewalLinkNotification($signedUrl));

            $count++;
        }

        return redirect()
            ->back()
            ->with('success', "$count seasonal renewal records generated and links sent.");
    }
}
