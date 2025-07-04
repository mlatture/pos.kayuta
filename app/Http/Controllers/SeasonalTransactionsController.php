<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

use App\Models\SeasonalAddOns;
use App\Models\User;
use App\Models\SeasonalRate;
use App\Models\SeasonalRenewal;
use App\Models\DocumentTemplate;

use App\Notifications\SeasonalRenewalLinkNotification;

use PhpOffice\PhpWord\TemplateProcessor;
use Barryvdh\DomPDF\Facade\Pdf;

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

        // Flatten all seasonal rate IDs from users

        $allRateIds = $recipients
            ->flatMap(function ($user) {
                return is_array($user->seasonal) ? $user->seasonal : json_decode($user->seasonal, true);
            })
            ->unique()
            ->filter()
            ->values()
            ->all();

        // Join and Get Seasonal Rate
        $seasonalRates = SeasonalRate::whereIn('id', $allRateIds)->get()->keyBy('id');

        DB::beginTransaction();

        try {
            foreach ($recipients as $user) {
                $seasonalIds = is_array($user->seasonal) ? $user->seasonal : json_decode($user->seasonal, true);

                foreach ($seasonalIds as $rateId) {
                    $rate = $seasonalRates->get($rateId);
                    if (!$rate) {
                        continue;
                    }

                    SeasonalRenewal::updateOrCreate(
                        [
                            'customer_id' => $user->id,
                            'offered_rate' => $rate->rate_price,
                            'status' => 'sent offer',
                        ],
                        [
                            'customer_name' => $user->name ?? trim("{$user->f_name} {$user->l_name}"),
                            'customer_email' => $user->email,
                            'allow_renew' => true,
                            'initial_rate' => $rate->rate_price,
                            'discount_percent' => null,
                            'discount_amount' => null,
                            'discount_note' => null,
                            'final_rate' => $rate->rate_price,
                            'payment_plan' => null,
                            'payment_plan_id' => null,
                            'selected_payment_method' => null,
                            'day_of_month' => null,
                            'renewed' => false,
                            'response_date' => null,
                            'notes' => 'Auto-created during offer send',
                        ],
                    );

                    URL::forceRootUrl('https://book.kayuta.com');
                    // URL::forceRootUrl('http://127.0.0.1:8001');
                    $signedUrl = URL::temporarySignedRoute('seasonal.renewal.guest', now()->addDays(14), ['user' => $user->id]);

                    $docsFile = DocumentTemplate::find($rate->template_id);
                    //Generate Contracts
                    if ($docsFile && file_exists(public_path("storage/{$docsFile->file}"))) {
                        $templatePath = public_path("storage/{$docsFile->file}");
                        $fileName = "contract_{$user->l_name}_{$user->id}.pdf";
                        $filePath = public_path("storage/contracts/{$docsFile->name}");
                        $filePath2 = public_path("storage/contracts/{$docsFile->name}/{$fileName}");
                        


                        if (!file_exists(dirname($filePath))) {
                            mkdir(dirname($filePath), 0775, true);
                        }

                        // $templateProcessor = new TemplateProcessor($templatePath);
                        // $templateProcessor->setValues([
                        //     'first_name' => $user->f_name,
                        //     'last_name' => $user->l_name,
                        //     'seasonal_rate' => "$" . number_format($rate->rate_price, 2),
                        //     'deadline' => optional($rate->final_payment_due)->format('F j, Y'),
                        // ]);
                        // $templateProcessor->saveAs($filePath2);

                        $pdf = Pdf::loadView('contracts.seasonal_contract', [
                            'first_name' => $user->f_name,
                            'last_name' => $user->l_name,
                            'site_number' => $user->site_number ?? null,
                            'email' => $user->email,
                            'initial_rate' => $rate->rate_price,
                            'discount_percent' => null, // or $rate->discount_percent
                            'discount_amount' => null, // or $rate->discount_amount
                            'final_rate' => $rate->rate_price,
                            'addons' => null,
                            'deadline' => optional($rate->final_payment_due)->format('F j, Y'),
                        ]);

                        $pdf->save("$filePath/{$fileName}");
                    }

                    // Send email notification
                    $user->notify(new SeasonalRenewalLinkNotification($signedUrl));
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Renewal entries created for seasonal customers.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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

   
    
}
