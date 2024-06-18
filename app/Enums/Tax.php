<?php

namespace App\Enums;

enum Tax: string
{
    /* Calculation Types */
    case CAL_TYPE_PER_GUEST = 'per_guest';
    case CAL_TYPE_SINGLE_CHARGE = 'single_charge';

    /* Calculation Charges */
    case CAL_CHARGE_PER_NIGHT = 'per_night';
    case CAL_CHARGE_PER_STAY = 'per_stay';

    /* Tax Types */
    case TYPE_INCLUSIVE = 'inclusive';
    case TYPE_EXCLUSIVE = 'exclusive';
    case TYPE_FLAT = 'flat';
    case TYPE_PERCENTAGE = 'percentage';

    /**
     * Get readable value
     * 
     * @return string
     */
    public function readable(): string
    {
        return match($this) {
            Tax::CAL_TYPE_PER_GUEST => 'Per Guest',
            Tax::CAL_TYPE_SINGLE_CHARGE => 'Single Charge',
            Tax::CAL_CHARGE_PER_NIGHT => 'Per Night',
            Tax::CAL_CHARGE_PER_STAY => 'Per Stay',
            Tax::TYPE_INCLUSIVE => 'Inclusive',
            Tax::TYPE_EXCLUSIVE => 'Exclusive',
            Tax::TYPE_FLAT => 'Flat',
            Tax::TYPE_PERCENTAGE => 'Percentage',
        };
    }

    /**
     * Get all tax types
     * 
     * @return array
     */
    public static function taxTypes(): array
    {
        return [
            Tax::TYPE_FLAT,
            Tax::TYPE_PERCENTAGE,
        ];
    }

    /**
     * Get all calculation types
     * 
     * @return array
     */
    public static function calculationTypes(): array
    {
        return [
            Tax::CAL_TYPE_PER_GUEST,
            Tax::CAL_TYPE_SINGLE_CHARGE,
        ];
    }

    /**
     * Get all calculation charges
     * 
     * @return array
     */
    public static function calculationCharges(): array
    {
        return [
            Tax::CAL_CHARGE_PER_NIGHT,
            Tax::CAL_CHARGE_PER_STAY,
        ];
    }

    /**
     * Get all tax inclusion options
     * 
     * @return array
     */
    public static function taxInclusionOptions(): array
    {
        return [
            Tax::TYPE_INCLUSIVE,
            Tax::TYPE_EXCLUSIVE,
        ];
    }
}