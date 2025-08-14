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
use App\Models\GuestFile;
use App\Models\SystemLog;
use App\Notifications\SeasonalRenewalLinkNotification;
use App\Notifications\NonRenewalNotification;
use App\Notifications\PaymentDeclinedNotification;
use App\Notifications\PaymentReceiptNotification;

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
            $hadRows = SeasonalRenewal::query()->exists();
            if ($hadRows) {
                SeasonalRenewal::truncate();

                return response()->json([
                    'success' => true,
                    'message' => 'Seasonal renewals cleared. No records were reloaded.',
                ]);
            }
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
                    SeasonalRenewal::create([
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
                'message' => 'Seasonal renewals loaded (table was empty).',
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

    public function sendEmails(Request $request)
    {
        $recipients = User::whereNotNull('seasonal')->whereRaw('JSON_LENGTH(seasonal) > 0')->get();
        $currentYear = now()->year;
        $seasonalRates = SeasonalRate::with('template')->get()->keyBy('id');

        try {
            DB::beginTransaction();
            foreach ($recipients as $user) {
                $seasonalIds = is_array($user->seasonal) ? $user->seasonal : json_decode($user->seasonal, true);
                if (!$seasonalIds || !is_array($seasonalIds)) {
                    continue;
                }

                $existingRenewal = SeasonalRenewal::where('customer_email', $user->email)->whereYear('created_at', $currentYear)->first();

                $status = 'sent offer';
                $rateForRenewal = null;

                foreach ($seasonalIds as $rateId) {
                    $r = $seasonalRates->get($rateId);
                    if (!$r) {
                        continue;
                    }
                    $tName = optional($r->template)->name;
                    if ($tName && Str::contains(Str::lower($tName), 'non-renewal')) {
                        $status = 'sent rejection';
                    }
                    $rateForRenewal = $r;
                    break;
                }

                if ($rateForRenewal) {
                    if (!($existingRenewal && $existingRenewal->allow_renew)) {
                        // create or update once
                        if ($existingRenewal) {
                            $existingRenewal->update([
                                'allow_renew' => true,
                                'status' => $status,
                                'initial_rate' => $rateForRenewal->rate_price,
                                'final_rate' => $rateForRenewal->rate_price,
                            ]);
                        } else {
                            SeasonalRenewal::create([
                                'customer_name' => $user->name ?? trim("{$user->f_name} {$user->l_name}"),
                                'customer_email' => $user->email,
                                'allow_renew' => true,
                                'status' => $status,
                                'initial_rate' => $rateForRenewal->rate_price,
                                'final_rate' => $rateForRenewal->rate_price,
                            ]);
                        }
                    }
                }

                // 2) Now process ALL seasonalIds → create files/logs per rate
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

                    // Category from rate_name/templateName
                    $rateName = (string) ($rate->rate_name ?? ($templateName ?? 'contract'));
                    $lower = Str::lower($rateName);
                    if (Str::contains($lower, ['renewal', 'seasonal'])) {
                        $category = 'renewals';
                    } elseif (Str::contains($lower, ['non-renewal', 'non renewal'])) {
                        $category = 'non_renewals';
                    } elseif (Str::contains($lower, ['contract', 'agreement'])) {
                        $category = 'contracts';
                    } elseif (Str::contains($lower, ['waiver', 'seasonal liability waiver '])) {
                        $category = 'waivers';
                    } elseif (Str::contains($lower, ['vaccine', 'vaccination', 'rabies'])) {
                        $category = 'vaccinations';
                    } elseif (Str::contains($lower, ['id', 'license', 'passport'])) {
                        $category = 'ids';
                    } else {
                        $category = Str::slug($rateName, '_');
                    }

                    $year = $currentYear;
                    // Make filename UNIQUE per rate (include rate id + slug)
                    $nameSlug = Str::slug($rateName, '_');
                    $fileBaseName = "{$category}_{$nameSlug}_rate{$rate->id}_{$user->l_name}_{$user->id}.docx";
                    $relativeDir = "{$category}/{$year}";
                    $relativeFsPath = "{$relativeDir}/{$fileBaseName}";
                    $publicDbPath = "storage/{$relativeFsPath}";

                    $absDir = public_path("storage/{$relativeDir}");
                    $absFile = public_path("storage/{$relativeFsPath}");
                    if (!is_dir($absDir)) {
                        mkdir($absDir, 0775, true);
                    }
                    if (!file_exists($absFile)) {
                        $sourcePath = public_path("storage/{$template->file}");
                        copy($sourcePath, $absFile);
                    }

                    // Expiration defaults
                    $expiration = null;
                    if (in_array($category, ['contracts', 'renewals'])) {
                        $expiration = now()->endOfYear();
                    }

                    GuestFile::updateOrCreate(
                        [
                            'customer_id' => $user->id,
                            'name' => $fileBaseName,
                            'file_category' => $category,
                            'file_path' => $relativeFsPath,
                        ],
                        [
                            'reservation_id' => null,
                            'expiration_date' => $expiration,
                        ],
                    );

                    // Log with your schema
                    SystemLog::create([
                        'transaction_type' => 'file_management',
                        'sale_amount' => null,
                        'status' => 'success', // or null if enum mismatch
                        'payment_type' => null,
                        'confirmation_number' => null,
                        'customer_name' => $user->name ?? trim("{$user->f_name} {$user->l_name}"),
                        'customer_email' => $user->email,
                        'user_id' => auth()->id(),
                        'description' => sprintf('Created/updated guest file: %s (category: %s, year: %s, rate_id: %d) for customer #%d', $publicDbPath, $category, $year, $rate->id, $user->id),
                        'before' => null,
                        'after' => [
                            'file_path' => $publicDbPath,
                            'file_name' => $fileBaseName,
                            'category' => $category,
                            'year' => $year,
                            'rate_id' => $rate->id,
                            'customer_id' => $user->id,
                        ],
                        'created_at' => now(),
                    ]);
                }

                // 3) Send ONE email per user (after processing all rates)
                if ($rateForRenewal) {
                    URL::forceRootUrl('https://book.kayuta.com');
                    $signedUrl = URL::temporarySignedRoute('seasonal.verify.guest', now()->addDays(14), ['user' => $user->id]);

                    if ($status === 'sent rejection') {
                        $user->notify(new NonRenewalNotification($signedUrl));
                    } else {
                        $user->notify(new SeasonalRenewalLinkNotification($signedUrl));
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Emails sent, renewals updated, and guest files recorded for all seasonal rates.',
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
        $downPayment = $validated['down_payment'] ?? 0;

        // ✅ FIXED: Start after today if down payment made
        $startDate = $downPayment > 0 ? now()->copy()->addMonth()->startOfDay() : Carbon::parse($rate->payment_plan_starts)->startOfDay();

        $endDate = Carbon::parse($rate->final_payment_due)->startOfDay();

        URL::forceRootUrl('https://book.kayuta.com');
        $signedUrl = URL::temporarySignedRoute('seasonal.verify.guest', now()->addDays(14), ['user' => $user->id]);
        $name = $user->f_name . ' ' . $user->l_name;
        $email = $user->email;

        // === FULL PAYMENT ===
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
                    'credit' => $cardknox->sale($validated['xCardNum'], $validated['xCVV'], $validated['xExp'], $discountedTotal, $name, $email),
                    'ach' => $cardknox->achSale($validated['xRouting'], $validated['xACH'], $validated['xName'], $discountedTotal, $name, $email),
                    default => ['xResult' => 'A'],
                };

                if (($response['xResult'] ?? '') !== 'A') {
                    DB::rollBack();
                    \Log::error('CardKnox Response:', $response);
                    $user->notify(new PaymentDeclinedNotification($response['xError'] ?? 'Payment failed.', $signedUrl));
                    return response()->json(['message' => $response['xError'] ?? 'Payment failed.', 'success' => false], 400);
                }

                ScheduledPayment::create([
                    'customer_email' => $user->email,
                    'customer_name' => $name,
                    'payment_date' => now(),
                    'amount' => $discountedTotal,
                    'paid_amount' => $discountedTotal,
                    'reference_key' => $response['xRefNum'] ?? null,
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

                $user->notify(new PaymentReceiptNotification($discountedTotal, $validated['payment_method'], 'paid_in_full', now(), $signedUrl));
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('CardKnox Error', ['exception' => $e->getMessage(), 'response' => $response ?? 'No response returned']);
                $user->notify(new PaymentDeclinedNotification($response ?? 'Payment failed.', $signedUrl));
                return response()->json(['message' => 'Error processing full payment: ' . $e->getMessage(), 'success' => false], 500);
            }
        }

        // === INSTALLMENT PLAN ===
        else {
            $remaining = $fullAmount - $downPayment;
            $months = $startDate->diffInMonths($endDate);

            if ($months <= 0) {
                return response()->json(['success' => false, 'message' => 'Invalid schedule: final payment date must be after start date.']);
            }

            $monthlyAmount = round($remaining / $months, 2);
            $totalScheduled = 0;

            DB::beginTransaction();
            try {
                // Process down payment now
                if ($downPayment > 0) {
                    $response = match ($validated['payment_method']) {
                        'credit' => $cardknox->sale($validated['xCardNum'], $validated['xCVV'], $validated['xExp'], $downPayment, $name, $email),
                        'ach' => $cardknox->achSale($validated['xRouting'], $validated['xACH'], $validated['xName'], $downPayment, $name, $email),
                        default => ['xResult' => 'A'],
                    };

                    if (($response['xResult'] ?? '') !== 'A') {
                        DB::rollBack();
                        $user->notify(new PaymentDeclinedNotification($response['xError'] ?? 'Payment failed.', $signedUrl));
                        return response()->json(['message' => $response['xError'] ?? 'Down payment failed.', 'success' => false], 400);
                    }

                    ScheduledPayment::create([
                        'customer_email' => $user->email,
                        'customer_name' => $name,
                        'payment_date' => now(),
                        'amount' => $downPayment,
                        'paid_amount' => $downPayment,
                        'reference_key' => $response['xRefNum'] ?? null,
                        'payment_type' => $validated['payment_method'],
                        'frequency' => 'none',
                        'status' => 'Completed',
                    ]);
                }

                // Schedule future payments starting next month
                for ($i = 0; $i < $months; $i++) {
                    $dueDate = $startDate->copy()->addMonths($i)->day($dayOfMonth);

                    // Handle short months (like Feb 30 fallback to Feb 28/29)
                    if ($dueDate->day != $dayOfMonth) {
                        $dueDate->endOfMonth();
                    }

                    $amount = $i === $months - 1 ? round($remaining - $totalScheduled, 2) : $monthlyAmount;

                    $totalScheduled += $amount;

                    ScheduledPayment::create([
                        'customer_email' => $user->email,
                        'customer_name' => $name,
                        'payment_date' => $dueDate,
                        'amount' => $amount,
                        'paid_amount' => 0.0,
                        'reference_key' => null,
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

                $user->notify(new PaymentReceiptNotification($downPayment, $validated['payment_method'], 'paid_deposit', now(), $signedUrl));
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $user->notify(new PaymentDeclinedNotification($response['xError'] ?? $e->getMessage(), $signedUrl));
                return response()->json(['success' => false, 'message' => 'Failed to create payment schedule: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['success' => true, 'message' => 'Payment setup complete! Redirecting in 3s...']);
    }

    public function storeRemainingBalance(Request $request, $user, CardknoxService $cardknox)
    {
        $request->validate(
            [
                'selected_card' => 'required|string',
                'cardholder_name' => 'nullable|string',
                'expiry' => 'required|string',
                'cvv' => 'required|max:4',
                'amount' => 'required|numeric|min:0.01',
            ],
            [
                'cvv.required' => 'CVV is required.',
                'amount.min' => 'Amount must be greater than zero.',
            ],
        );

        $userModel = User::findOrFail($user);
        $amountToApply = floatval($request->amount);

        // Get unpaid scheduled payments
        $scheduled = ScheduledPayment::where('customer_email', $userModel->email)->where('status', 'Pending')->orderBy('payment_date')->get();

        if ($scheduled->isEmpty()) {
            return response()->json(['error' => 'No unpaid scheduled payments found.'], 422);
        }

        $earliest = $scheduled->first();
        if ($amountToApply < $earliest->amount) {
            return response()->json(
                [
                    'error' => 'Amount entered must be at least $' . number_format($earliest->amount, 2),
                ],
                422,
            );
        }

        DB::beginTransaction();

        try {
            // CardKnox Payment
            $cardNumber = $request->selected_card;
            $cvv = $request->cvv;
            $expiry = $request->expiry;
            $name = $request->cardholder_name ?? '';
            $email = $userModel->email;

            $isACH = str_starts_with($cardNumber, 'xRouting');
            $isToken = strlen($cardNumber) > 20;

            if ($isACH) {
                $response = $cardknox->achSale($amountToApply, $cardNumber, $name);
            } elseif ($isToken) {
                $response = $cardknox->saveSale($cardNumber, $cvv, $amountToApply, $name, $email);
            } else {
                $response = $cardknox->sale($cardNumber, $cvv, $expiry, $amountToApply, $name, $email);
            }

            if (!$response['success']) {
                DB::rollBack();
                return response()->json(['error' => $response['message'] ?? 'Payment failed'], 400);
            }

            // Apply payment
            $earliestDue = $earliest->amount;
            $excess = $amountToApply - $earliestDue;

            // Update earliest row
            $earliest->update([
                'paid_amount' => $amountToApply,
                'status' => 'Completed',
            ]);

            // If overpaid, apply to next row
            if ($excess > 0 && $scheduled->count() > 1) {
                $next = $scheduled[1];
                $nextPaid = $next->paid_amount ?? 0;
                $nextDue = $next->amount - $nextPaid;
                $newPaid = $nextPaid + $excess;

                $next->update([
                    'paid_amount' => $newPaid,
                    'amount' => $nextDue - $excess,
                    'status' => $newPaid >= $next->amount ? 'Completed' : 'Pending',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment of $' . number_format($amountToApply, 2) . ' applied successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Remaining balance payment error', [
                'user' => $user,
                'error' => $e->getMessage(),
            ]);

            return response()->json(
                [
                    'error' => 'An unexpected error occurred during payment. Please try again or contact support.',
                ],
                500,
            );
        }
    }
}
