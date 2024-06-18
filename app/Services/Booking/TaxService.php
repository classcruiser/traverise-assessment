<?php

namespace App\Services\Booking;

use App\Enums\Tax;
use App\Models\Booking\Booking;
use App\Models\Booking\CustomTax;
use App\Models\Booking\CustomTaxSetting;
use Illuminate\Database\Eloquent\Collection;

class TaxService
{
    /**
     * Calculate subtotal with the tax amount
     *
     * @param float $amount
     * @param float $percentage
     * @return float
     */
    public static function calculateTax(float $amount, float $percentage): float
    {
        if ($amount <= 0) return (float) 0;

        $add = floatVal(100 + $percentage);

        return round((($amount / $add) * $percentage), 2);
    }

    /**
     * Calculate amount without the tax amount
     *
     * @param float $amount
     * @param float $percentage
     * @return float
     */
    public static function getAmountWithoutTax(float $price, float $tax_percentage, bool $applicable = true): float
    {
        if (!$applicable) {
            return $price;
        }

        return $price > 0 ? round($price - self::calculateTax($price, $tax_percentage), 2) : 0;
    }

    /**
     * Get list of active taxes and separate them into inclusive and exclusive
     *
     * @return array
     */
    public static function getActiveTaxes(Booking|null $booking = null): array
    {
        if ($booking) {
            $location_ids = $booking->location->taxes?->pluck('custom_tax_id')->toArray();
            $accommodation_ids = $booking->rooms->map(fn ($room) => $room->room->taxes?->pluck('custom_tax_id'))->flatten()->unique()->toArray();
            $addon_ids = $booking->rooms->map(fn ($room) => $room->addons->map(fn ($addon) => $addon->details->taxes?->pluck('custom_tax_id')))->flatten()->unique()->toArray();

            $combined_ids = collect([...$location_ids, ...$accommodation_ids, ...$addon_ids])->unique()->toArray();
        }

        $taxes = CustomTax::where('is_active', 1)
            ->when($booking, fn ($query) => $query->whereIn('id', $combined_ids))
            ->orderBy('sort', 'asc')
            ->get();

        $inclusives = $taxes->filter(fn ($tax) => $tax->tax_type == Tax::TYPE_INCLUSIVE->value);
        $exclusives = $taxes->filter(fn ($tax) => $tax->tax_type == Tax::TYPE_EXCLUSIVE->value);

        return [
            'total_active_tax' => $inclusives->count() + $exclusives->count(),
            'inclusives' => [
                'total' => $inclusives->count(),
                'taxes' => $inclusives->all(),
            ],
            'exclusives' => [
                'total' => $exclusives->count(),
                'taxes' => $exclusives->all(),
            ],
        ];
    }

    /**
     * Calculate the percentage difference of a given amount from the total
     *
     * @param float $total
     * @param float $amount
     * @return float
     */
    public static function calculatePercentageDifference(float $total, float $amount): float
    {
        return (($amount / $total) * 100);
    }

    /**
     * Check if booking has inclusive tax
     *
     * @param Booking $booking
     * @return bool
     */
    public static function bookingHasInclusiveTax(Booking $booking): bool
    {
        // Check if the booking has inclusive tax
    }

    /**
     * Calculate the tax amount for the booking
     *
     * @param array $taxes
     * @param Booking $booking
     * @return array
     */
    public static function calculateBookingTaxes(array $taxes, Booking $booking): array
    {
        $array = [
            'vat' => [
                'accommodations' => [],
                'addons' => []
            ],
            'others' => [
                'locations' => null,
            ],
            'empty' => true
        ];

        $inc_taxes = collect($taxes['inclusives']['taxes']);
        $addon_taxes = [];
        $accommodation_taxes = [];

        $room_prices = $booking->rooms->sum('price');

        $booking->rooms->each(function ($room) use ($inc_taxes, &$accommodation_taxes, &$addon_taxes) {
            $room->room->taxes->each(function ($tax) use ($inc_taxes, &$accommodation_taxes, $room) {
                $tax = $inc_taxes->where('id', $tax->custom_tax_id)->first();
                if (!isset($accommodation_taxes[$tax->id])) {
                    $accommodation_taxes[$tax->id] = [
                        'rate' => 0,
                        'amount' => 0,
                    ];
                }
                if (!$tax) {
                    return 0;
                }

                $accommodation_taxes[$tax->id]['rate'] = ($tax->type == Tax::TYPE_FLAT->value ? '&euro; ' : '') . number_format($tax->rate, 0) . ($tax->type == Tax::TYPE_PERCENTAGE->value ? '%' : '');
                $accommodation_taxes[$tax->id]['amount'] += $tax->rate ? self::calculateTax($room->price ?? 0, $tax->rate) : 0;
            });

            $room->addons->each(function ($addon) use ($inc_taxes, &$addon_taxes) {
                $addon->details->taxes->each(function ($tax) use ($inc_taxes, &$addon_taxes, $addon) {
                    $tax = $inc_taxes->where('id', $tax->custom_tax_id)->first();
                    if (!isset($addon_taxes[$tax->id])) {
                        $addon_taxes[$tax->id] = [
                            'rate' => 0,
                            'amount' => 0,
                        ];
                    }
                    if (!$tax) {
                        return 0;
                    }

                    $addon_taxes[$tax->id]['rate'] = ($tax->type == Tax::TYPE_FLAT->value ? '&euro; ' : '') . number_format($tax->rate, 0) . ($tax->type == Tax::TYPE_PERCENTAGE->value ? '%' : '');
                    $addon_taxes[$tax->id]['amount'] += $tax->rate ? self::calculateTax($addon->price ?? 0, $tax->rate) : 0;
                });
            });
        });

        if (count($accommodation_taxes) || count($addon_taxes)) {
            $array['empty'] = false;
        }

        return [
            'vat' => [
                'accommodations' => $accommodation_taxes,
                'addons' => $addon_taxes,
            ],
            'empty' => $array['empty'],
        ];
    }

    /**
     * Get the exclusive tax list
     *
     * @param Booking $booking
     * @return array
     */
    public static function calculateExclusiveTax(float $amount, float $rate, string $type): float
    {
        if ($type == Tax::TYPE_FLAT->value) {
            return $rate;
        }

        if ($type == Tax::TYPE_PERCENTAGE->value) {
            return $amount * ($rate / 100);
        }

        return 0.00;
    }

    public static function calculateTotalExclusiveTax(Booking $booking): float
    {
        $tax_total = 0;
        $taxes = $booking->location->taxes;

        $total = $booking->subtotal_with_discount;

        foreach ($taxes as $tax) {
            if ($tax->tax->tax_type == Tax::TYPE_EXCLUSIVE->value && $tax->tax->type == Tax::TYPE_PERCENTAGE->value) {
                $tax_total += $total * $tax->tax->rate / 100;
            }
        }

        return $tax_total;
    }
}
