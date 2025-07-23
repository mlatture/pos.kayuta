<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\CPU\ImageManager;

use App\Models\RateTier;
use App\Models\SeasonalSetting;
use App\Models\SeasonalRenewal;
use App\Models\User;
use App\Models\DocumentTemplate;
use App\Models\SeasonalRate;
use App\Models\SeasonalAddOns;

use App\Notifications\SeasonalRenewalLinkNotification;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

use App\Helpers\FileHelper;

class SeasonalSettingController extends Controller
{
    //

    public function index()
    {
        $documentTemplates = DocumentTemplate::all();
        $seasonalAddOns = SeasonalAddOns::all();
        $seasonalRates = SeasonalRate::with('template')->get();
        $currentYear = now()->year;

        $renewals = SeasonalRenewal::whereYear('created_at', $currentYear)->with('customer')->latest()->get();

        // Reset Renewals
        $currentYearRenewalsCount = $renewals
            ->filter(function ($renewal) use ($currentYear) {
                return optional($renewal->created_at)->year === $currentYear;
            })
            ->count();

        $showResetWarning = $currentYearRenewalsCount === 0;

        return view('admin.seasonal.index', compact('seasonalAddOns', 'documentTemplates', 'seasonalRates', 'renewals', 'showResetWarning', 'currentYearRenewalsCount'));
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:doc,docx,pdf',
        ]);

        $path = FileHelper::storeFile($request->file('file'), 'templates', $validated['name']);

        DocumentTemplate::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'file' => $path,
            'is_active' => true,
        ]);

        return back()->with('success', 'Template uploaded successfully.');
    }

    public function storeRate(Request $request)
    {
        $validated = $request->validate([
            'rate_name' => 'required|string|max:255|unique:seasonal_rates,rate_name',
            'rate_price' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'early_pay_discount' => 'nullable|numeric|min:0',
            'full_payment_discount' => 'nullable|numeric|min:0',
            'payment_plan_starts' => 'nullable|date',
            'final_payment_due' => 'nullable|date',
            'template_id' => 'nullable|exists:document_templates,id',
            'applies_to_all' => 'nullable|boolean',
            'active' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();
            $rate = new SeasonalRate();
            $rate = $rate->fill($validated);
            $rate->applies_to_all = $request->has('applies_to_all');
            $rate->active = $request->has('active');
            $rate->save();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to save seasonal rate: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Seasonal Rate saved successfully!');
    }

    public function updateRate(Request $request, $id)
    {
        $rate = SeasonalRate::findOrFail($id);

        $validated = $request->validate([
            'rate_name' => 'required|string|max:255|unique:seasonal_rates,rate_name,' . $rate->id,
            'rate_price' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'early_pay_discount' => 'nullable|numeric|min:0',
            'full_payment_discount' => 'nullable|numeric|min:0',
            'payment_plan_starts' => 'nullable|date',
            'final_payment_due' => 'nullable|date',
            'template_id' => 'nullable|exists:document_templates,id',
            // 'applies_to_all' => 'nullable|boolean',
            'active' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $rate->fill($validated);
            // $rate->applies_to_all = $request->has('applies_to_all');
            $rate->active = $request->has('active');
            $rate->save();

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Rate updated successfully!']);
            }

            // Fallback (non-AJAX)
            return redirect()->back()->with('success', 'Seasonal Rate updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Failed to update seasonal rate: ' . $e->getMessage());
        }
    }

    public function destroyRate($id)
    {
        $rate = SeasonalRate::findOrFail($id);

        try {
            DB::beginTransaction();


            $rate->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Seasonal Rate deleted successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete seasonal rate: ' . $e->getMessage()], 500);
        }
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
            $templatePath = public_path('storage/templates/contract_template.docx');
            $fileName = "contract_{$user->l_name}_{$user->id}.docx";
            $filePath = public_path("storage/contracts/$fileName");

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

    public function destroy(DocumentTemplate $template)
    {
        // $seasonalRates = SeasonalRate::where('template_id', $template->id)->get();

        // foreach ($seasonalRates as $rate) {
        //     $rate->delete();
        // }

        try {
            DB::beginTransaction();

            if ($template->file) {
                ImageManager::delete($template->file);
            }

            $template->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to delete Document Template: ' . $e->getMessage(),
                ],
                500,
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Document Template deleted successfully!',
        ]);
    }

    // Download Generated Contracts PDF
    public function downloadExistingContract($filename)
    {
        $path = public_path("storage/contracts/{$filename}");

        if (file_exists($path)) {
            return response()->download($path);
        }

        abort(404, 'Contract not found.');
    }
}
