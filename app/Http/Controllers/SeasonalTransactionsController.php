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

use App\Services\CardKnoxService;

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

    public function clearAndReload()
    {
        $recipients = User::whereNotNull('seasonal')->whereRaw('JSON_LENGTH(seasonal) > 0')->get();
        $currentYear = now()->year;
        $seasonalRates = SeasonalRate::with('template')->get()->keyBy('id');
        $documentTemplates = DocumentTemplate::all()->keyBy('id');

        try {
            DB::beginTransaction();

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

                    $discount_percent = $rate->early_pay_discount + $rate->full_payment_discount ?? 0;
                    SeasonalRenewal::updateOrCreate([
                        'customer_name' => $user->name ?? trim("{$user->f_name} {$user->l_name}"),
                        'customer_email' => $user->email,
                        'allow_renew' => false,
                        'status' => $status,
                        'initial_rate' => $rate->rate_price,
                        'discount_percent' => $discount_percent ?? null,
                        'discount_amount' => null,
                        'discount_note' => null,
                        'final_rate' => $rate->rate_price,
                        'payment_plan' => null,
                        'selected_card' => null,
                        'day_of_month' => null,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Seasonal renewals cleared and reloaded successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('clearAndReload error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to clear and reload seasonal renewals.',
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
                $seasonalIds = is_array($user->seasonal) ? $user->seasonal : json_decode($user->seasonal, true);
                if (!$seasonalIds || !is_array($seasonalIds)) {
                    continue;
                }

                foreach ($seasonalIds as $rateId) {
                    $rate = $seasonalRates->get($rateId);
                    if (!$rate) {
                        continue;
                    }

                    $template = $rate->template;
                    $templateName = $template->name ?? null;

                    if (!$template || !file_exists(public_path("storage/{$template->file}"))) {
                        \Log::warning("Missing template for user {$user->email}");
                        continue;
                    }

                    // Check if renewal already exists for current year
                    $existingRenewal = SeasonalRenewal::where('customer_email', $user->email)->whereYear('created_at', $currentYear)->first();

                    // Skip if exists and allow_renew is already true
                    if ($existingRenewal && $existingRenewal->allow_renew) {
                        continue;
                    }

                    // Update or create logic
                    $status = Str::contains(Str::lower($templateName), 'non-renewal') ? 'sent rejection' : 'sent offer';

                    if ($existingRenewal) {
                        // Update existing record
                        $existingRenewal->update([
                            'allow_renew' => true,
                            'status' => $status,
                            'initial_rate' => $rate->rate_price,
                            'final_rate' => $rate->rate_price,
                        ]);
                    } else {
                        // Create new renewal record
                        SeasonalRenewal::create([
                            'customer_name' => $user->name ?? trim("{$user->f_name} {$user->l_name}"),
                            'customer_email' => $user->email,
                            'allow_renew' => true,
                            'status' => $status,
                            'initial_rate' => $rate->rate_price,
                            'final_rate' => $rate->rate_price,
                        ]);
                    }

                    // Generate contract
                    $fileName = "contract_{$user->l_name}_{$user->id}.pdf";
                    $contractFolder = public_path("storage/contracts/{$templateName}");

                    if (!file_exists($contractFolder)) {
                        mkdir($contractFolder, 0775, true);
                    }

                    $destinationPath = "{$contractFolder}/{$fileName}";
                    if (!file_exists($destinationPath)) {
                        $sourcePath = public_path("storage/{$template->file}");
                        copy($sourcePath, $destinationPath);
                    }

                    // Send email
                    URL::forceRootUrl('https://book.kayuta.com');
                    $signedUrl = URL::temporarySignedRoute('seasonal.verify.guest', now()->addDays(14), ['user' => $user->id]);

                    if (Str::contains(Str::lower($templateName), 'non-renewal')) {
                        $user->notify(new \App\Notifications\NonRenewalNotification($signedUrl));
                    } else {
                        $user->notify(new \App\Notifications\SeasonalRenewalLinkNotification($signedUrl));
                    }

                    \Log::info("Processed seasonal renewal for {$user->email}");
                    break; // only use first matching seasonal rate
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Emails sent and renewals updated where applicable.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('sendEmails error: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to send renewal emails.',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    // Through Api Transactions (It Triggers from Book.kayuta.com)

    public function storeScheduledPayments(User $user, SeasonalRate $rate, Request $request, CardKnoxService $cardknox)
    {
        $validated = $request->validate([
            'payment_type' => 'required|in:full,installments',
            'payment_method' => 'required|in:credit,ach,in_store,mailed_check',
            'down_payment' => 'nullable|numeric|min:0',
            'xCardNum' => 'nullable|string',
            'xCVV' => 'nullable|string',
            'xExp' => 'nullable|string',
            'xACH' => 'nullable|string',
            'xRouting' => 'nullable|string',
            'xName' => 'nullable|string',
        ]);

        $renewal = SeasonalRenewal::where('customer_email', $user->email)->latest()->first();
        if (!$renewal) {
            return response()->json(['message' => 'Renewal record not found.'], 404);
        }

        $rate = SeasonalRate::where('rate_price', $renewal->initial_rate)->latest()->first();

        if (!$rate) {
            return response()->json(['message' => 'Seasonal rate not found.'], 404);
        }

        $fullAmount = $renewal->final_rate;
        $dayOfMonth = $renewal->day_of_month ?? 5;
        $startDate = Carbon::parse($rate->payment_plan_starts);
        $endDate = Carbon::parse($rate->final_payment_due);

        // FULL PAYMENT PROCESSING
        if ($validated['payment_type'] === 'full') {
            $earlyDiscount = $rate->early_pay_discount ?? 0;
            $fullDiscount = $rate->full_payment_discount ?? 0;
            $totalDiscountPercent = $earlyDiscount + $fullDiscount;

            $discountAmount = $fullAmount * ($totalDiscountPercent / 100);
            $discountedTotal = round($fullAmount - $discountAmount, 2);

            $response = null;

            try {
                DB::beginTransaction();

                $response = match ($validated['payment_method']) {
                    'credit' => $cardknox->sale($validated['xCardNum'], $validated['xCVV'], $validated['xExp'], $discountedTotal),
                    'ach' => $cardknox->achSale($validated['xRouting'], $validated['xACH'], $validated['xName'], $discountedTotal),
                    default => ['xResult' => 'A'],
                };

                if (($response['xResult'] ?? '') !== 'A') {
                    \Log::error('CardKnox Response:', $response);

                    DB::rollBack();
                    return response()->json(
                        [
                            'message' => $response['xError'] ?? 'Payment failed.',
                            'success' => false,
                        ],
                        400,
                    );
                }

                ScheduledPayment::create([
                    'customer_email' => $user->email,
                    'customer_name' => $user->f_name . ' ' . $user->l_name,
                    'payment_date' => now(),
                    'amount' => $discountedTotal,
                    'payment_type' => $validated['payment_method'],
                    'frequency' => 'none',
                    'status' => 'Completed',
                ]);

                $renewal->update([
                    'payment_plan' => 'paid_in_full',
                    'status' => 'paid in full',
                    'renewed' => true,
                    'selected_card' => $validated['payment_method'],
                    'day_of_month' => $dayOfMonth,
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                \Log::error('CardKnox Error', [
                    'exception' => $e->getMessage(),
                    'response' => $response ?? 'No response returned',
                ]);

                return response()->json(
                    [
                        'message' => 'Error processing full payment: ' . $e->getMessage(),
                        'success' => false,
                    ],
                    500,
                );
            }
        }
        // INSTALLMENT PLAN PROCESSING
        else {
            $downPayment = $validated['down_payment'] ?? 0;
            $remaining = $fullAmount - $downPayment;
            $months = $startDate->diffInMonths($endDate);

            if ($months <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid schedule: final payment date must be after start date.',
                ]);
            }

            $monthlyAmount = round($remaining / $months, 2);
            $totalScheduled = 0;

            DB::beginTransaction();
            try {
                // Process down payment now (if provided)
                if ($downPayment > 0) {
                    $response = match ($validated['payment_method']) {
                        'credit' => $cardknox->sale($validated['xCardNum'], $validated['xCVV'], $validated['xExp'], $downPayment),
                        'ach' => $cardknox->achSale($validated['xRouting'], $validated['xACH'], $validated['xName'], $downPayment),
                        default => ['xResult' => 'A'],
                    };

                    if (($response['xResult'] ?? '') !== 'A') {
                        DB::rollBack();
                        return response()->json(
                            [
                                'message' => $response['xError'] ?? 'Down payment failed.',
                                'success' => false,
                            ],
                            400,
                        );
                    }

                    ScheduledPayment::create([
                        'customer_email' => $user->email,
                        'customer_name' => $user->f_name . ' ' . $user->l_name,
                        'payment_date' => now(),
                        'amount' => $downPayment,
                        'payment_type' => $validated['payment_method'],
                        'frequency' => 'none',
                        'status' => 'Completed',
                    ]);
                }

                // Schedule future payments
                for ($i = 0; $i < $months; $i++) {
                    $dueDate = $startDate->copy()->addMonths($i)->day($dayOfMonth);
                    if ($dueDate->lt($startDate)) {
                        $dueDate->addMonth();
                    }

                    $amount = $i === $months - 1 ? round($remaining - $totalScheduled, 2) : $monthlyAmount;
                    $totalScheduled += $amount;

                    ScheduledPayment::create([
                        'customer_email' => $user->email,
                        'customer_name' => $user->f_name . ' ' . $user->l_name,
                        'payment_date' => $dueDate,
                        'amount' => $amount,
                        'payment_type' => $validated['payment_method'],
                        'frequency' => 'monthly',
                        'status' => 'Pending',
                    ]);
                }

                $renewal->update([
                    'payment_plan' => 'monthly_' . $validated['payment_method'],
                    'status' => 'paid deposit',
                    'selected_card' => $validated['payment_method'],
                    'day_of_month' => $dayOfMonth,
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Failed to create payment schedule: ' . $e->getMessage(),
                    ],
                    500,
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment setup complete! Redirecting in 3s...',
        ]);
    }
}
