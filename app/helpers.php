<?php

use Carbon\Carbon;

if (!function_exists('activeSegment')) {
    function activeSegment($name, $segment = 2, $class = 'active')
    {
        return request()->segment($segment) == $name ? $class : '';
    }
}

function calculateGiftCardDiscount($giftCard, $amount = 0)
{
    try {
        if ($giftCard->min_purchase > $amount) {
            throw new \Exception('Gift card is not applicable! Minimum purchase amount is $' . number_format($giftCard->min_purchase,2));
        }

        $discount = 0;
        if ($giftCard->discount_type == 'percentage') {
            $discount   =   $amount * ($giftCard->discount / 100);
        } else if ($giftCard->discount_type == 'fixed_amount') {
            $discount   =   $giftCard->discount;
        }

        return $discount;
    } catch (Exception $exception) {
        throw new \Exception($exception->getMessage());
    }
}

function generateLinearCalendar($start_date, $end_date)
{
    $calendar = [];

  
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);

    $current = clone $start;
    while ($current <= $end) {
        $calendar[] = $current->format('Y-m-d');
        $current->modify('+1 day');
    }

    return $calendar;
}

function calculatePerProductTax($product)
{
    $tax    =   0;
    if ($product->taxType) {
        if ($product->taxType->tax_type == 'fixed_amount') {
            $tax  =   $product->taxType->tax;
        } else if ($product->taxType->tax_type == 'percentage') {
            $tax  =   ($product->price * $product->taxType->tax) / 100;
        }
    }

    return $tax;
}

function calculatePerProductDiscount($product)
{
    $discount   =   0;
    if ($product->discount_type == 'fixed_amount') {
        $discount  =   $product->discount;
    } else if ($product->discount_type == 'percentage') {
        $discount  =   ($product->price * $product->discount) / 100;
    }

    return $discount;
}

function calculatePerProductCartAmount($product)
{
    $amount = 0;
    $amount =   (intval($product->price) * $product->pivot->quantity) - ($product->pivot->discount ?? 0) + ($product->pivot->tax ?? 0);

    return $amount;
}

function countNumberOfDays($startDate, $endDate)
{
    $start = Carbon::parse($startDate);
    $end = Carbon::parse($endDate);

    // Calculate the difference in days
    $days = $end->diffInDaysFiltered(function (Carbon $date) use ($start, $end) {
        return $date->between($start, $end);
    });

    return $days > 0 ? $days : 0;
}
