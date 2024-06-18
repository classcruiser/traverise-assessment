<?php

declare(strict_types=1);

function calculateTax($percentage, $amount): float {
    $add = floatVal(100 + $percentage);

    return (($amount / $add) * $percentage);
}

if (!function_exists('parseAddonPrice')) {
    function parseAddonPrice(float $price, float $tax_percentage) {
        if (!auth()->check() || $tax_percentage <= 0) {
            return $price;
        }
        
        $price = auth()->user()->tax['goods_tax'] ? $price : $price - calculateTax($tax_percentage, $price);
        return round($price, 2);
    }
}

if (!function_exists('parsePrice')) {
    function parsePrice(float $price) {
        return number_format(round($price, 2), 2);
    }
}