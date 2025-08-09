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
use App\Models\ScheduledPayment;

use App\Notifications\SeasonalRenewalLinkNotification;
use App\Notifications\NonRenewalNotification;

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
        $documentTemplates = DocumentTemplate::all()->keyBy('id');
        $seasonalAddOns = SeasonalAddOns::all();
        $seasonalRates = SeasonalRate::with('template')->get()->keyBy('id');
        $currentYear = now()->year;

        $renewals = SeasonalRenewal::whereYear('created_at', $currentYear)->with('customer')->latest()->get();

        $nonRenewals = collect();
        $filteredRenewals = collect();

        foreach ($renewals as $renewal) {
            $user = $renewal->customer;
            if (!$user || !$user->seasonal) {
                continue;
            }

            $seasonalIds = is_array($user->seasonal) ? $user->seasonal : json_decode($user->seasonal, true);

            $matchedRate = null;

            foreach ($seasonalIds ?? [] as $rateId) {
                $rate = $seasonalRates->get($rateId);
                if (!$rate) {
                    continue;
                }

                // Match by rate price or similarity
                if ((float) $rate->rate_price == (float) $renewal->initial_rate) {
                    $matchedRate = $rate;
                    break;
                }
            }

            $templateName = strtolower($matchedRate->template->name ?? '');
            $rateName = strtolower($matchedRate->rate_name ?? '');

            $isNonRenewal = Str::contains($templateName, 'non-renewal') || Str::contains($rateName, 'non-renewal');
            $isRenewal = !Str::contains($templateName, 'non-renewal') && !Str::contains($rateName, 'non-renewal');
            if ($isNonRenewal) {
                $nonRenewals->push($renewal);
            } elseif ($isRenewal) {
                $filteredRenewals->push($renewal);
            }
        }

        $currentYearRenewalsCount = $filteredRenewals->count();
        $showResetWarning = $currentYearRenewalsCount === 0;

        return view('admin.seasonal.index', compact('seasonalAddOns', 'documentTemplates', 'seasonalRates', 'filteredRenewals', 'nonRenewals', 'showResetWarning', 'currentYearRenewalsCount', 'renewals'));
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:doc,docx',
        ]);

        $path = FileHelper::storeFile($request->file('file'), 'templates', $validated['name']);

        DocumentTemplate::create([
            'name' => $validated['name'],
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
            // 'applies_to_all' => 'nullable|boolean',
            'active' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();
            $rate = new SeasonalRate();
            $rate = $rate->fill($validated);
            // $rate->applies_to_all = $request->has('applies_to_all');
            $rate->active = $request->has('active');
            $rate->save();

            DB::commit();

            return redirect()->back()->with('success', 'Seasonal Rate saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to save seasonal rate: ' . $e->getMessage());
        }
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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Document Template deleted successfully!',
            ]);
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

    public function statements(ScheduledPayment $scheduledPayment, $email)
    {
        $statements = $scheduledPayment->where('customer_email', $email)->orderBy('payment_date', 'asc')->get();

        if ($statements->isEmpty()) {
            return redirect()->back()->with('error', 'No statements found for this customer.');
        }

        return view('admin.seasonal.statements', compact('statements'));
    }

    public function viewContract($email) 
    {
        $user = User::where('email', $email)->first();

        $seasonal = is_string($user->seasonal) ? json_decode($user->seasonal, true) : $user->seasonal;
        $rateIds = collect($seasonal)->filter()->values()->all();

        $rates = SeasonalRate::with('template')->whereIn('id', $rateIds)->get();
        foreach($rates as $rate){
            $fileName = 'contracts/' . $rate->template->name . '/contract_' . $user->l_name . '_' . $user->id . '_signed' . '.docx';
            $template = $rate->template->file;


        }

        

        return view('admin.seasonal.contract', compact('user', 'rates', 'fileName', 'template'));
    }
}
