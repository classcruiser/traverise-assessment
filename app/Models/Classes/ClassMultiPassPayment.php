<?php

namespace App\Models\Classes;

use App\Models\Booking\Guest;
use App\Models\Booking\Location;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ClassMultiPassPayment extends Model
{
    use BelongsToTenant;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = ['total_paid', 'open_balance', 'open_balance_with_fee'];

    public function multiPass()
    {
        return $this->belongsTo(ClassMultiPass::class, 'class_multi_pass_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id', 'id');
    }

    public function guestWhoOrdered()
    {
        return $this->belongsTo(Guest::class, 'guest_who_ordered', 'id');
    }

    public function records()
    {
        return $this->hasMany(ClassMultiPassPaymentRecord::class, 'multi_pass_payment_id', 'id');
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

    public function getStatusBadgeAttribute()
    {
        $css = match ($this->status) {
            'PARTIAL' => 'bg-grey-600',
            'PENDING' => 'bg-blue',
            'DUE' => 'badge-warning',
            'DRAFT' => 'bg-grey-400',
            'EXPIRED' => 'alpha-danger text-grey-400',
            'COMPLETED' => 'bg-success',
            'RESERVED' => 'bg-teal',
            'CANCELLED' => 'bg-danger',
            'ABANDONED' => 'alpha-grey text-grey-300',
            default => ''
        };

        return $css;
    }

    public static function generateUniqCode (int $length = 8): string
    {
        $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random = substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
        $isExists = self::where('activation_code', $random)->get()->count() > 0;

        if ($isExists) {
            return self::generateUniqCode();
        }

        return $random;
    }
}
