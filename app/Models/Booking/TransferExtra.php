<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TransferExtra extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    public function rooms()
    {
        return $this->hasMany(RoomTransfer::class);
    }

    public function prices()
    {
        return $this->hasMany(TransferExtraPrice::class);
    }

    public function taxes()
    {
        return $this
            ->hasMany(CustomTaxSetting::class, 'model_id', 'id')
            ->where('model_path', self::class);
    }
}
