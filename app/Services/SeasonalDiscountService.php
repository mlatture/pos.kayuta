<?php

namespace App\Services;

use Illuminate\Support\Collection;

class SeasonalDiscountService
{
    /**
     *
     * @param float $baseRate;
     * @param array|Collection $discounts;
     * @return array ['final' => float, 'percent_removed' => float, 'dollar_removed' => float]
     */

    public static function applyTo(float $baseRate, $discounts): array
    {
        $discounts = collect($discounts)->filter(fn($dis) => $dis->is_active ?? ($dis['is_active'] ?? true));

        $percentDiscounts = $discounts->where('discount_type', 'percentage');
        $dollarDiscounts = $discounts->where('discount_type', 'dollar');

        $remaining = $baseRate;
        $percentRemoved = 0.0;
        $dollarRemoved = 0.0;

        foreach ($percentDiscounts as $d) {
            $p = (float) ($d->discount_value ?? $d['discount_value']);
            if ($p <= 0) {
                continue;
            }
            $removed = $remaining * ($p / 100.0);
            $remaining -= $removed;
            $percentRemoved += $removed;
        }

        foreach ($dollarDiscounts as $d) {
            $a = (float) ($d->discount_value ?? $d['discount_value']);
            if ($a <= 0) {
                continue;
            }
            $toRemove = min($a, $remaining);
            $remaining -= $toRemove;
            $dollarRemoved += $toRemove;
        }

        return [
            'final' => round(max(0.0, $remaining), 2),
            'percent_removed' => round($percentRemoved, 2),
            'dollar_removed' => round($dollarRemoved, 2),
        ];
    }

    /**
     *
     * @param float $baseRate;
     * @param array|Collection $discounts;
     * @return array ['final' => float, 'percent_removed' => float, 'dollar_removed' => float]
     */

    public static function previewTotals(float $baseRate, $discounts): array
    {
        $discounts = collect($discounts)->filter(fn($d) => $d->is_active ?? ($d['is_active'] ?? true));

        $percentDiscounts = $discounts->where('discount_type', 'percentage');
        $dollarDiscounts = $discounts->where('discount_type', 'dollar');

        $remaining = $baseRate;
        $percentRemoved = 0.0;

        foreach ($percentDiscounts as $d) {
            $p = (float) ($d->discount_value ?? $d['discount_value']);
            if ($p <= 0) {
                continue;
            }
            $removed = $remaining * ($p / 100.0);
            $remaining -= $removed;
            $percentRemoved += $removed;
        }

        $dollarRequested = $dollarDiscounts->sum(fn($d) => (float) ($d->discount_value ?? $d['discount_value']));

        return [
            'percent_removed' => round($percentRemoved, 2),
            'dollar_requested' => round($dollarRequested, 2),
            'total_requested_removal' => round($percentRemoved + $dollarRequested, 2),
        ];
    }

    /**
    
     * @param float $baseRate
     * @param array|Collection $discounts
     * @return array ['steps' => [...], 'final' => float]
     */
    public static function breakdown(float $baseRate, $discounts): array
    {
        $discounts = collect($discounts)->filter(fn($d) => $d->is_active ?? ($d['is_active'] ?? true));
        $ordered = $discounts->sortBy(fn($d) => $d->discount_type === 'percentage' ? 0 : 1)->values();

        $steps = [];
        $remaining = $baseRate;

        foreach ($ordered as $d) {
            $type = $d->discount_type;
            $value = (float) ($d->discount_value ?? $d['discount_value']);
            $desc = $d->description ?? ($d['desc'] ?? null);
            if ($type === 'percentage') {
                $removed = $remaining * ($value / 100.0);
                $remaining -= $removed;
                $steps[] = [
                    'type' => 'percentage',
                    'value' => $value,
                    'description' => $desc,
                    'removed' => round($removed, 2),
                    'remaining_after' => round($remaining, 2),
                ];
            } else {
                $toRemove = min($value, $remaining);
                $remaining -= $toRemove;
                $steps[] = [
                    'type' => 'dollar',
                    'value' => $value,
                    'description' => $desc,
                    'removed' => round($toRemove, 2),
                    'remaining_after' => round($remaining, 2),
                ];
            }
        }

        return ['steps' => $steps, 'final' => round(max(0.0, $remaining), 2)];
    }
}
