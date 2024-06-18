<?php

namespace App\Models\Classes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ClassPayment extends Model
{
    use BelongsToTenant;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = ['total_paid', 'open_balance', 'open_balance_with_fee'];

    public function booking()
    {
        return $this->belongsTo(ClassBooking::class, 'class_booking_id', 'id')->withTrashed();
    }

    public function records()
    {
        return $this->hasMany(ClassPaymentRecord::class);
    }

    public function getTotalPaidAttribute()
    {
        $records = $this->records;

        return $records->sum(function ($record) {
            return ($record->verify_by && $record->verified_at) ? floatval($record->amount) : 0;
        });
    }

    public function getOpenBalanceAttribute()
    {
        $total_paid = $this->total_paid;
        $total = floatval($this->total);

        return $total - $total_paid;
    }

    public function getOpenBalanceWithFeeAttribute()
    {
        $total_paid = $this->total_paid;
        $total = floatval($this->total);
        $processing_fee = floatval($this->processing_fee);

        return $total + $processing_fee - $total_paid;
    }
}
