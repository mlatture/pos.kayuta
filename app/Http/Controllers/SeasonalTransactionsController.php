<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use App\Models\SeasonalAddOns;
use App\Models\User;
use App\Models\SeasonalRate;
use App\Models\SeasonalRenewal;
use App\Models\DocumentTemplate;
use App\Models\ScheduledPayment;
use App\Notifications\SeasonalRenewalLinkNotification;
use App\Notifications\NonRenewalNotification;

use PhpOffice\PhpWord\TemplateProcessor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SeasonalTransactionsController extends Controller
{
    public function storeAddOns(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_allowed' => 'nullable|integer|min:1',
            'active' => 'nullable|in:0,1',
        ]);

        try {
            DB::beginTransaction();

            SeasonalAddOns::create([
                'seasonal_add_on_name' => $validated['name'],
                'seasonal_add_on_price' => $validated['price'],
                'max_allowed' => $validated['max_allowed'] ?? 1,
                'active' => isset($validated['active']) && $validated['active'] === 'on' ? 1 : 0,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Add-On created successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to save Add-On.',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function destroyAddOn(SeasonalAddOns $addon)
    {
        try {
            DB::beginTransaction();
            $addon->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Seasonal Add-On deleted successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to Delete Add-On',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    // Send Emails to the Seasonals Customers

    public function sendEmails(Request $request)
    {
        $recipients = User::whereNotNull('seasonal')->whereRaw('JSON_LENGTH(seasonal) > 0')->get();

        $currentYear = now()->year;
        $seasonalRates = SeasonalRate::with('template')->get()->keyBy('id');
        $documentTemplates = DocumentTemplate::all()->keyBy('id');

        DB::beginTransaction();

        try {
            foreach ($recipients as $user) {
                $alreadyRenewed = SeasonalRenewal::where('customer_email', $user->email)->whereYear('created_at', $currentYear)->exists();

                if ($alreadyRenewed) {
                    continue;
                }

                $seasonalIds = is_array($user->seasonal) ? $user->seasonal : json_decode($user->seasonal, true);
                if (!$seasonalIds || !is_array($seasonalIds)) {
                    continue;
                }

                foreach ($seasonalIds as $rateId) {
                    $rate = $seasonalRates->get($rateId);
                    if (!$rate) {
                        continue;
                    }

                    $status = Str::contains(Str::lower($rate->template->name ?? ''), 'non-renewal') || Str::contains(Str::lower($rate->rate_name ?? ''), 'sent rejection') ? 'sent rejection' : 'sent offer';

                    SeasonalRenewal::updateOrCreate([
                        'customer_name' => $user->name ?? trim("{$user->f_name} {$user->l_name}"),
                        'customer_email' => $user->email,
                        'allow_renew' => true,
                        'status' => $status,
                        'initial_rate' => $rate->rate_price,
                        'discount_percent' => null,
                        'discount_amount' => null,
                        'discount_note' => null,
                        'final_rate' => $rate->rate_price,
                        'payment_plan' => null,
                        'selected_card' => null,
                        'day_of_month' => null,
                    ]);

                    // Determine matched template
                    $matchedTemplate = null;
                    $matchedTemplateName = null;

                    if ($rate->template && isset($documentTemplates[$rate->template_id])) {
                        $matchedTemplate = $documentTemplates[$rate->template_id];
                        $matchedTemplateName = $matchedTemplate->name;
                    }

                    if (!$matchedTemplate || !file_exists(public_path("storage/{$matchedTemplate->file}"))) {
                        \Log::warning("Missing template for user {$user->email}");
                        continue;
                    }

                    // Generate contract PDF
                    $templatePath = public_path("storage/{$matchedTemplate->file}");
                    $fileName = "contract_{$user->l_name}_{$user->id}.pdf";
                    $contractFolder = public_path("storage/contracts/{$matchedTemplate->name}");

                    if (!file_exists($contractFolder)) {
                        mkdir($contractFolder, 0775, true);
                    }

                    $pdf = Pdf::loadView('contracts.seasonal_contract', [
                        'first_name' => $user->f_name,
                        'last_name' => $user->l_name,
                        'site_number' => $user->site_number ?? null,
                        'email' => $user->email,
                        'initial_rate' => $rate->rate_price,
                        'discount_amount' => $rate->discount_amount ?? null,
                        'final_rate' => $rate->rate_price,
                        'addons' => null,
                        'deadline' => optional($rate->final_payment_due)->format('F j, Y'),
                    ]);

                    $pdf->save("$contractFolder/{$fileName}");

                    URL::forceRootUrl('https://book.kayuta.com');
                    $signedUrl = URL::temporarySignedRoute('seasonal.verify.guest', now()->addDays(14), ['user' => $user->id]);

                    \Log::info("Sending renewal to {$user->email}, using template: {$matchedTemplateName}");

                    if (Str::contains(Str::lower($matchedTemplateName), 'non-renewal')) {
                        $user->notify(new NonRenewalNotification($signedUrl));
                    } else {
                        $user->notify(new SeasonalRenewalLinkNotification($signedUrl));
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Renewal entries created for seasonal customers.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('sendEmails error: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to create renewal records.',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    // Through Api Transactions (It Triggers from Book.kayuta.com)
    public function storeScheduledPayments(User $user, SeasonalRate $rate, Request $request)
    {
        $validated = $request->validate([
            'payment_type' => 'required|in:full,installments', // User selection UI (not stored as-is)
            'payment_method' => 'required|in:credit,ach,in_store,mailed_check', // Match ENUM: 'credit', 'ach'
            'down_payment' => 'nullable|numeric|min:0',
        ]);

        $renewal = SeasonalRenewal::where('customer_email', $user->email)->latest()->first();
        if (!$renewal) {
            return response()->json(['Renewal record not found.']);
        }

        $rate = SeasonalRate::where('rate_price', $renewal->initial_rate)->latest()->first();
        if (!$rate) {
            return response()->json(['Seasonal rate not found.']);
        }

        $map = [
            'credit' => 'credit',
            'ach' => 'ach',
        ];

        $paymentMethod = $map[$request->payment_method];

        $fullAmount = $renewal->final_rate;
        $dayOfMonth = $renewal->day_of_month ?? 5; // fallback
        $startDate = Carbon::parse($rate->payment_plan_starts);
        $endDate = Carbon::parse($rate->final_payment_due);

        if ($validated['payment_type'] === 'full') {
            // Full Payment - frequency = none
            ScheduledPayment::create([
                'customer_email' => $user->email,
                'customer_name' => $user->f_name . ' ' . $user->l_name,
                'payment_date' => now(),
                'amount' => $fullAmount,
                'payment_type' => $paymentMethod, // 'ach' or 'credit'
                'frequency' => 'none',
                'status' => 'Completed',
            ]);

            $renewal->update([
                'payment_plan' => $paymentMethod === 'ach' ? 'paid_in_full' : 'paid_in_full',
                'status' => 'paid in full',
                'day_of_month' => $dayOfMonth,
                'renewed' => true,
                'selected_card' => $paymentMethod,
            ]);
        } else {
            $downPayment = $validated['down_payment'] ?? 0;
            $remaining = $fullAmount - $downPayment;
            $months = $startDate->diffInMonths($endDate);
            if ($months <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid payment schedule: final payment date must be after start date.',
                ]);
            }

            $monthlyAmount = round($remaining / $months, 2);
            $totalScheduled = 0;

            DB::beginTransaction();
            try {
                if ($downPayment > 0) {
                    ScheduledPayment::create([
                        'customer_email' => $user->email,
                        'customer_name' => $user->f_name . ' ' . $user->l_name,
                        'payment_date' => now(),
                        'amount' => $downPayment,
                        'payment_type' => $paymentMethod,
                        'frequency' => 'none',
                        'status' => 'Completed',
                    ]);
                }

                for ($i = 0; $i < $months; $i++) {
                    $due = $startDate->copy()->addMonths($i)->day($dayOfMonth);
                    if ($due->lt($startDate)) {
                        $due->addMonth();
                    }

                    // Handle rounding adjustment for last month
                    $amount = $i === $months - 1 ? round($remaining - $totalScheduled, 2) : $monthlyAmount;

                    $totalScheduled += $amount;

                    ScheduledPayment::create([
                        'customer_email' => $user->email,
                        'customer_name' => $user->f_name . ' ' . $user->l_name,
                        'payment_date' => $due,
                        'amount' => $amount,
                        'payment_type' => $paymentMethod,
                        'frequency' => 'monthly',
                        'status' => 'Pending',
                    ]);
                }

                $renewal->update([
                    'payment_plan' => $paymentMethod === 'ach' ? 'monthly_ach' : 'monthly_credit',
                    'status' => 'paid deposit',
                    'selected_card' => $paymentMethod,
                    'day_of_month' => $dayOfMonth,
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment schedule: ' . $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment setup complete! Redirecting in 3s...',
        ]);
    }
}
