<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class LocationPaymentMethod extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $guarded = [];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function details()
    {
        return $this->hasMany(LocationPaymentMethodDetail::class, 'location_payment_method_id', 'id');
    }
}
