<?php

namespace App\Models\Booking;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Payment extends Model
{
    use BelongsToTenant;
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at', 'deposit_due_date'];

    protected $appends = ['total_paid', 'open_balance', 'open_balance_with_fee'];

    protected $with = ['records'];

    protected $withCount = ['records'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function dueDays()
    {
        $deposit_date = new Carbon($this->deposit_due_date);
        $today = new Carbon(date('Y-m-d').' 00:00:00');

        return $today->diffInDays($deposit_date);
    }

    public function records()
    {
        return $this->hasMany(PaymentTransfer::class);
    }

    public function getTotalPaidAttribute()
    {
        $records = $this->records;

        return $records->sum(function ($record) {
            return ($record->verify_by && $record->verified_at) ? floatval($record->amount) : 0;
        });
    }

    public function getProcessingFeeAttribute($value)
    {
        return !in_array($this->methods, ['cash', 'banktransfer']) ? $value : 0;
    }

    public function getOpenBalanceAttribute()
    {
        $total_paid = $this->total_paid;
        $total_booking = $this->total;
        $commission = 0;
        $balance = 0;

        return round(floatval($total_booking) - floatval($total_paid), 2);
    }

    public function calculateProcessingFee($grand_total = null)
    {
        $profile = Profile::where('tenant_id', tenant('id'))->first();
        $amount = $grand_total ?? $this->open_balance;

        if ($amount <= 0) {
            $amount = $this->total;
        }

        $fee_1 = $profile?->stripe_fee_percentage ? $amount * ($profile->stripe_fee_percentage / 100) : 0;
        $fee_2 = $profile?->stripe_fee_fixed ? $profile->stripe_fee_fixed : 0;

        return round($fee_1 + $fee_2, 2);
    }

    public function getOpenBalanceWithFeeAttribute()
    {
        $amount = $this->open_balance;

        return round($amount + $this->calculateProcessingFee(), 2);
    }
}
