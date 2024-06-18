<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomTaxSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tax()
    {
        return $this->belongsTo(CustomTax::class, 'custom_tax_id', 'id');
    }
}
