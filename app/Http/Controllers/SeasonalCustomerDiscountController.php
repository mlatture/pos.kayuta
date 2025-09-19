<?php

namespace App\Http\Controllers;

use App\Models\SeasonalCustomerDiscount;
use App\Models\User;
use App\Models\SystemLog;
use App\Services\SeasonalDiscountService;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class SeasonalCustomerDiscountController extends Controller
{
    public function index(Request $request, $customerId)
    {
        $year = (int) $request->get('year', date('Y'));
        $customer = User::findOrFail($customerId);

        $discounts = SeasonalCustomerDiscount::where('customer_id', $customerId)->where('season_year', $year)->orderBy('id')->get();

        $baseRate = $request->get('base_rate') !== null ? (float) $request->get('base_rate') : null;

        $applied = $baseRate !== null ? SeasonalDiscountService::applyTo($baseRate, $discounts) : ['final' => null, 'percent_removed' => 0.0, 'dollar_removed' => 0.0];

        return view('admin.seasonal.discounts.index', compact('customer', 'year', 'discounts', 'baseRate', 'applied'));
    }

    public function store(Request $request)
    {
        // basic validation
        $data = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'discount_type' => 'required|in:percentage,dollar',
            'discount_value' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
            'is_active' => 'required|boolean',
            'season_year' => ['required', 'digits:4', 'integer', 'min:1901', 'max:2155'],
            'base_rate' => 'required|numeric|min:0.01',
        ]);

        $customerId = (int) $data['customer_id'];
        $year = (int) $data['season_year'];
        $baseRate = (float) $data['base_rate'];

        if ($baseRate <= 0.0) {
            return back()
                ->withErrors([
                    'base_rate' => 'Base rate must be greater than zero. Discounts only apply to paid seasonal rates.',
                ])
                ->withInput();
        }

        if ($data['discount_type'] === 'percentage') {
            $p = (float) $data['discount_value'];
            if ($p <= 0 || $p >= 100) {
                return back()
                    ->withErrors([
                        'discount_value' => 'Percentage discount must be greater than 0 and less than 100. Use smaller value or use dollar discount.',
                    ])
                    ->withInput();
            }
        }

        $duplicate = SeasonalCustomerDiscount::where('customer_id', $customerId)->where('season_year', $year)->where('is_active', true)->where('discount_type', $data['discount_type'])->where('discount_value', $data['discount_value'])->where('description', $data['description'])->exists();

        if ($duplicate) {
            return back()
                ->withErrors([
                    'duplicate' => 'An identical active discount already exists for this customer and season.',
                ])
                ->withInput();
        }

        $existing = SeasonalCustomerDiscount::where('customer_id', $customerId)->where('season_year', $year)->where('is_active', true)->get();

        $preview = $existing->concat([
            (object) [
                'discount_type' => $data['discount_type'],
                'discount_value' => $data['discount_value'],
                'description' => $data['description'],
                'is_active' => ($data['is_active'] = filter_var($request->input('is_active', false), FILTER_VALIDATE_BOOLEAN)),
            ],
        ]);

        $previewTotals = SeasonalDiscountService::previewTotals($baseRate, $preview);

        if (!isset($previewTotals['total_requested_removal'])) {
            return back()
                ->withErrors([
                    'internal' => 'Could not calculate preview totals. Please contact the system administrator.',
                ])
                ->withInput();
        }

        if ($previewTotals['total_requested_removal'] - $baseRate > 0.0001) {
            return back()
                ->withErrors([
                    'discount_value' => 'Combined discounts would exceed 100% of the base rate. Adjust values.',
                ])
                ->withInput();
        }

        try {
            $data['created_by'] = Auth::id();
            $discount = new SeasonalCustomerDiscount($data);
            $discount->save();

            $after = [
                'discount_id' => $discount->id,
                'customer_id' => $discount->customer_id,
                'season_year' => $discount->season_year,
                'type' => $discount->discount_type,
                'value' => $discount->discount_value,
                'description' => $discount->description,
                'is_active' => $discount->is_active,
                'created_by' => $discount->created_by,
                'created_at' => $discount->created_at->toDateTimeString(),
                'base_rate' => $baseRate,
                'preview_totals' => $previewTotals,
            ];

            SystemLog::create([
                'transaction_type' => 'seasonal_customer_discount.created',
                'sale_amount' => $discount->discount_value,
                'status' => 'Success',
                'confirmation_number' => null,
                'customer_name' => optional($discount->customer)->f_name . ' ' . optional($discount->customer)->l_name,
                'customer_email' => optional($discount->customer)->email,
                'user_id' => Auth::id(),
                'description' => 'Created seasonal discount ID #' . $discount->id . ' for ' . $year,
                'before' => null,
                'after' => json_encode($after),
            ]);

            return redirect()->back()->with('success', 'Discount added successfully.');
        } catch (\Throwable $e) {
            SystemLog::create([
                'transaction_type' => 'seasonal_customer_discount.create_failed',
                'sale_amount' => $data['discount_value'] ?? null,
                'status' => 'Failed',
                'confirmation_number' => null,
                'customer_name' => optional(SeasonalCustomerDiscount::make($data)->customer)->f_name ?? null,
                'customer_email' => optional(SeasonalCustomerDiscount::make($data)->customer)->email ?? null,
                'user_id' => Auth::id(),
                'description' => 'Failed to create seasonal discount for customer_id ' . $customerId . ' for ' . $year . ': ' . $e->getMessage(),
                'before' => null,
                'after' => json_encode(['attempt' => $data, 'preview' => $previewTotals ?? null]),
            ]);

            \Log::error('Seasonal discount create failed: ' . $e->getMessage(), ['exception' => $e]);

            return back()
                ->withErrors([
                    'exception' => 'Failed to create discount due to a server error. The team has been notified.',
                ])
                ->withInput();
        }
    }

    public function deactivate($id)
    {
        $d = SeasonalCustomerDiscount::findOrFail($id);
        $d->is_active = false;
        $d->save();

        SystemLog::create([
            'transaction_type' => 'seasonal_customer_discount.deactivated',
            'description' => 'Deactivated seasonal discount ID #' . $d->id . ' for ' . $d->season_year,
            'user_id' => Auth()->user()->id,

            'before' => json_encode(['discount_id' => $d->id, 'customer_id' => $d->customer_id, 'season_year' => $d->season_year, 'is_active' => true]),
            'after' => json_encode(['discount_id' => $d->id, 'customer_id' => $d->customer_id, 'season_year' => $d->season_year, 'is_active' => false]),
        ]);

        return redirect()->back()->with('success', 'Discount deactivated successfully.');
    }

    public function destroy($id)
    {
        $d = SeasonalCustomerDiscount::findOrFail($id);
        $d->delete();

        $details = [
            'details' => json_encode([
                'discount_id' => $d->id,
                'customer_id' => $d->customer_id,
                'season_year' => $d->season_year,
                'type' => $d->discount_type,
                'value' => $d->discount_value,
                'description' => $d->description,
                'is_active' => $d->is_active,
                'created_by' => $d->created_by,
                'created_at' => $d->created_at,
            ]),
        ];

        SystemLog::create([
            'transaction_type' => 'seasonal_customer_discount.deleted',
            'description' => 'Deleted seasonal discount ID #' . $d->id . ' for ' . $d->season_year,
            'user_id' => Auth()->user()->id,

            'before' => $details,
            'after' => null,
        ]);

        return redirect()->back()->with('success', 'Discount deleted successfully.');
    }
}
