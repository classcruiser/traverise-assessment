<?php

namespace App\Models\Classes;

use App\Models\Booking\GuestCredit;
use App\Models\Booking\Location;
use App\Models\Booking\Profile;
use App\Models\Booking\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ClassBooking extends Model
{
    use BelongsToTenant;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'booking_date' => 'date:Y-m-d',
        'checked_in_at' => 'date:Y-m-d H:i:s',
    ];

    protected $appends = [
        'subtotal_with_discount',
        'total_price',
        'total_addons_price',
        'grand_total',
        'people',
        'status_badge'
    ];

    protected static function booted()
    {
        static::created(function ($booking) {
            // create QR code
            $filename = "{$booking->ref}-{$booking->id}";
            $link = url("class-bookings/details/{$filename}");

            try {
                QrCode::format('png')->size('300')->generate($link, public_path("qr-session/{$filename}.png"));
            } catch (\Exception $e) {
                // send to log
                Log::info('Cannot create QR code. Reason: ' . $e->getMessage());
            }
        });
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function guest(): HasOne
    {
        return $this->hasOne(ClassBookingGuest::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(ClassGuest::class);
    }

    public function people(): int
    {
        return $this->guests->groupBy('email')->count();
    }

    public function getPeopleAttribute(): float
    {
        return $this->people();
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassGuest::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(ClassBookingAddon::class);
    }

    public function credit(): HasOne
    {
        return $this->hasOne(GuestCredit::class, 'id', 'guest_credit_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(ClassPayment::class);
    }

    public function pass(): BelongsTo
    {
        return $this->belongsTo(ClassMultiPass::class, 'class_multi_passes_id', 'id');
    }

    public function checkedInUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by_id', 'id');
    }

    public function getSubTotalAttribute(): float
    {
        return $this->sessions->sum(fn ($session) => $session->price) ?? 0;
    }

    public function getTotalPriceAttribute(): float
    {
        $subtotal = $this->subtotal;
        $addons = $this->total_addons_price;

        return $subtotal + $addons;
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ClassBookingHistory::class)->orderBy('created_at', 'desc');
    }

    public function log(
        string $action,
        string $details,
        string $info = 'info',
        bool $withUser = true
    ): ClassBookingHistory {
        return $this->histories()->create([
            'user_id' => $withUser ? auth()->id() : null,
            'info_type' => $info,
            'action' => $action,
            'details' => $withUser ? '<b>' . auth()->user()->name . '</b> ' . $details : $details,
            'ip_address' => request()->ip(),
        ]);
    }

    public function getStatusBadgeAttribute()
    {
        $status = (
            'DRAFT' == $this->status ||
            'EXPIRED' == $this->status ||
            'CANCELLED' == $this->status ||
            'PENDING' == $this->status ||
            'ABANDONED' == $this->status ||
            'RESERVED' == $this->status || 
            'DELETED' == $this->status
        ) ? $this->status : $this->payment->status;

        $css = match ($status) {
            'PARTIAL' => 'bg-grey-600',
            'PENDING' => 'bg-blue',
            'DUE' => 'badge-warning',
            'DRAFT' => 'bg-grey-400',
            'EXPIRED' => 'alpha-danger text-grey-400',
            'COMPLETED' => 'bg-success',
            'RESERVED' => 'bg-teal',
            'CANCELLED' => 'bg-danger',
            'DELETED' => 'bg-danger',
            'ABANDONED' => 'alpha-grey text-grey-300',
            default => ''
        };

        return $css;
    }

    public function getScheduleDescriptionAttribute()
    {
        $days = [
            'MON' => 'Monday',
            'TUE' => 'Tuesday',
            'WED' => 'Wednesday',
            'THU' => 'Thursday',
            'FRI' => 'Friday',
            'SAT' => 'Saturday',
            'SUN' => 'Sunday',
        ];

        if ($this->schedule) {
            $start = substr($this->schedule->start, 0, 5);
            $end = substr($this->schedule->end, 0, 5);

            return "{$days[$this->schedule->day]}, {$start} - {$end}";
        }

        return '--';
    }

    public function getProcessingFeeAttribute()
    {
        $profile = Profile::where('tenant_id', tenant('id'))->first();
        $open_balance = $this->payment?->open_balance;

        $fee_1 = $profile->stripe_fee_percentage ? $open_balance * ($profile->stripe_fee_percentage / 100) : 0;
        $fee_2 = $profile->stripe_fee_fixed ? $profile->stripe_fee_fixed : 0;

        return $fee_1 + $fee_2;
    }

    protected function getTotalAddonsPriceAttribute(): float
    {
        //$this->load('addons');

        return $this->addons->sum(fn ($addon) => $addon->amount * $addon->price) ?? 0;
    }

    public function getRoomTaxAttribute()
    {
        return floatVal(number_format($this->subtotal_with_discount * $this->location->goods_tax / 100, 2));
    }

    public function getGrandTotalAttribute()
    {
        // room and duration discount total
        $session_price = $this->subtotal;

        // addons price
        $addons_price = $this->total_addons_price;

        // count discount here
        $discounts = $this->discounts;

        $total_discount = 0;

        $voucher_value = $this->discount_value;

        $total_price = $session_price + $addons_price;

        if ($discounts) {
            foreach ($discounts as $discount) {
                if ('Percent' == $discount->type) {
                    if ('ROOM' == $discount->apply_to) {
                        $total_discount += floatval(number_format($session_price * ($discount->value / 100), 2));
                    } else {
                        $total_discount += floatval(number_format($total_price * ($discount->value / 100), 2));
                    }
                } else {
                    $total_discount += $discount->value;
                }
            }
        }

        return $total_price - $total_discount - $voucher_value;
    }

    public function getSubtotalWithDiscountAttribute(): float
    {
        //$this->load('guests.session',  'guests.schedule', 'sessions.schedule.instructor', 'sessions.session.category');

        $sessions = $this->sessions;

        $session_price = 0;
        $total_discount = 0;

        $session_price = $sessions->sum(fn ($session) => $session->price);
        $discounts = null; // TO-DO : get discounts from class_booking_discounts table

        if ($discounts) {
            foreach ($discounts as $discount) {
                if ('Percent' == $discount->type) {
                    if ('ROOM' == $discount->apply_to) {
                        $total_discount += floatval(number_format($session_price * ($discount->value / 100), 2));
                    } else {
                        $total_discount += floatval(number_format($session_price * ($discount->value / 100), 2));
                    }
                } else {
                    $total_discount += $discount->value;
                }
            }
        }

        return $session_price - $total_discount;
    }

    public function getBookingStatusAttribute()
    {
        $status = (
            'DRAFT' == $this->status ||
            'EXPIRED' == $this->status ||
            'CANCELLED' == $this->status ||
            'ABANDONED' == $this->status ||
            'RESERVED' == $this->status
        ) ? $this->status : $this->payment->status;

        if ($this->status == 'DELETED' || $this->deleted_at) {
            $status = 'DELETED';
        }

        return $status;
    }

    public function getIsCancelledAttribute()
    {
        return $this->status == 'CANCELLED';
    }

    /**
     * Is booking confirmed.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function isConfirmed(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attribute) => !blank($attribute['status']) && $attribute['status'] === 'CONFIRMED',
        );
    }
}
