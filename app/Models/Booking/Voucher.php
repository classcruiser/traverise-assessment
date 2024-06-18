<?php

namespace App\Models\Booking;

use App\Models\Traits\Activeable;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Voucher extends Model
{
    use BelongsToTenant;
    use Activeable;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'integer',
        'amount_type' => 'string',
        'usage_limit' => 'integer',
        'expired_at' => 'date',
        'is_active' => 'boolean'
    ];

    public function rooms()
    {
        return $this->hasMany('App\Models\VoucherRoom');
    }
}
