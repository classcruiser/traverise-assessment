<?php

namespace App\Models\Booking;

use App\Services\Booking\TaxService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\Factory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use BelongsToTenant;
    use SoftDeletes;
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return \Database\Factories\BookingFactory::new();
    }

    protected $guarded = [];

    protected $with = ['rooms.discounts', 'rooms.addons.details.questionnaire.type', 'rooms.mainGuest.details.details', 'transfers', 'discounts', 'user'];

    protected $withCount = ['rooms'];

    protected $appends = [
        //'total_guests',
        'booking_status',
        'status_badge',
        'subtotal',
        'subtotal_with_discount',
        'grand_total',
        'total_duration_discount',
        'deposit_amount',
        'commission',
        'total_price',
        'total_transfers',
        'total_addons_price',
        'total_addons_price_with_applicable_tax',
        'created_at_simple',
        'days_until_stay',
        'commission_value',
        'archived',
    ];

    protected $dates = ['created_at', 'updated_at', 'expiry', 'check_in', 'check_out', 'deposit_expiry', 'created_at_simple', 'checked_in_at'];

    protected static function booted()
    {
        static::created(function ($booking) {
            // create QR code
            $filename = "{$booking->ref}-{$booking->id}";
            $link = url("bookings/details/{$filename}");

            try {
                QrCode::format('png')->size('300')->generate($link, public_path("qr/{$filename}.png"));
            } catch (\Exception $e) {
                // send to log
                Log::info('Cannot create QR code. Tenant ID: '. $booking->tenant_id .'. Reason: '. $e->getMessage());
            }
        });
    }

    public function createQRCode()
    {
        $filename = "{$this->ref}-{$this->id}";
        $link = url("bookings/details/{$filename}");

        try {
            QrCode::format('png')->size('300')->generate($link, public_path("qr/{$filename}.png"));
        } catch (\Exception $e) {
            // send to log
            Log::info('Cannot create QR code. Tenant ID: '. $this->tenant_id .'. Reason: '. $e->getMessage());
        }
    }

    public function location()
    {
        return $this->belongsTo(Location::class)->withDefault([
            'name' => '---',
            'short_name' => '---',
        ]);
    }

    public function specialPackage()
    {
        return $this->belongsTo(SpecialPackage::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class)->withTrashed();
    }

    public function payment_records()
    {
        return $this->hasManyThrough(PaymentTransfer::class, Payment::class);
    }

    public function payment_asc()
    {
        return $this->hasOne(Payment::class)->orderBy('total', 'asc');
    }

    public function payment_desc()
    {
        return $this->hasOne(Payment::class)->orderBy('total', 'desc');
    }

    public function cancellation()
    {
        return $this->hasOne(BookingCancellation::class);
    }

    public function histories()
    {
        return $this->hasMany(BookingHistory::class)->withTrashed()->orderBy('created_at', 'desc');
    }

    public function notes()
    {
        return $this->hasMany(BookingNote::class);
    }

    public function surf_planner_users()
    {
        return $this->hasMany(SurfPlannerUser::class)->where('type', 'register');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'source_id', 'id');
    }

    public function guest()
    {
        return $this->hasOne(BookingGuest::class)->where('group_id', 0);
    }

    public function other_guests()
    {
        return $this->hasMany(BookingGuest::class)->where('group_id', '!=', 0);
    }

    public function guests()
    {
        return $this->hasMany(BookingGuest::class);
    }

    public function getTotalGuestsAttribute()
    {
        return $this->guests()->count();
    }

    public function history()
    {
        return $this->hasMany(History::class)->withTrashed()->latest();
    }

    public function drivers()
    {
        return $this->hasMany(BookingDriver::class);
    }

    public function transfers()
    {
        return $this->hasMany(BookingTransfer::class)->withTrashed();
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id', 'id')->withDefault([
            'id' => null,
            'name' => '---',
            'commission_value' => 0,
        ]);
    }

    public function passports()
    {
        return $this->hasMany(BookingPassport::class, 'booking_id', 'id');
    }

    public function rooms()
    {
        return $this->hasMany(BookingRoom::class)->withTrashed();
    }

    public function checklists()
    {
        return $this->hasMany(BookingChecklist::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class)->withTrashed();
    }

    public function emails()
    {
        return $this->hasMany(EmailHistory::class)->withTrashed();
    }

    public function scopeOverview($query)
    {
        return $query->with(['guests.rooms.room.subroom', 'guests.rooms.room.addons.details', 'guest.details']);
    }

    public function getCreatedAtSimpleAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }

    public function getCommissionValueAttribute()
    {
        return 'Agent' == $this->source_type ? $this->user->commission_value : $this->agent->commission_value;
    }

    public function getArchivedAttribute()
    {
        return false;
    }

    public function parsePrice($value)
    {
        return $value;
        //return number_format($value, 2);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'tenant_id', 'tenant_id');
    }

    public function getProcessingFeeAttribute()
    {
        $profile = Profile::where('tenant_id', tenant('id'))->first();
        $open_balance = $this->payment?->open_balance;

        $fee_1 = $profile->stripe_fee_percentage ? $open_balance * ($profile->stripe_fee_percentage / 100) : 0;
        $fee_2 = $profile->stripe_fee_fixed ? $profile->stripe_fee_fixed : 0;

        return $fee_1 + $fee_2;
    }

    public function getPaidProcessingFeeAttribute()
    {
        $profile = Profile::where('tenant_id', tenant('id'))->first();
        $amount = $this->payment->total_paid;

        $fee_1 = $profile->stripe_fee_percentage ? $amount * ($profile->stripe_fee_percentage / 100) : 0;
        $fee_2 = $profile->stripe_fee_fixed ? $profile->stripe_fee_fixed : 0;

        return !in_array($this->payment->methods, ['cash', 'banktransfer']) ? ($fee_1 + $fee_2) : 0;
    }

    public function totalExtensionPrice()
    {
        $total_price = 0;

        $rooms = $this->rooms()->with(['addons'])->where('from', '>', $this->check_in)->get();

        $addons_price = $this->totalAddonsPrice($rooms);
        $duration_discount = $this->totalDurationDiscountPrice($rooms);
        $rooms_price = $rooms->sum(function ($room) {
            return $room->price;
        });

        return $rooms_price - $duration_discount + $addons_price;
    }

    public function getDaysUntilStayAttribute()
    {
        $check_in = new Carbon($this->check_in);
        $today = new Carbon(date('Y-m-d').' 00:00:00');

        if ($today->gt($check_in)) {
            return 0;
        }

        return $today->diffInDays($check_in);
    }

    public function getTotalTransfersAttribute()
    {
        $transfers = $this->transfers;
        $total = 0;

        if ($transfers) {
            $total = $transfers->sum(function ($transfer) {
                return $transfer['price'];
            });
        }

        return $total;
    }

    public function getCommissionAttribute()
    {
        if ('Agent' == $this->source_type || null != $this->agent_id) {
            $total = $this->grand_total;
            $comm = $this->commission_value;

            return floatval(number_format($total * ($comm / 100), 2));
        }

        return intval(0);
    }

    public function getVendorCommissionAttribute()
    {
        $value = 30;

        if ('Agent' == $this->source_type || !is_null($this->agent_id)) {
            $agent_commission = $this->commission_value;

            $value = $value - $agent_commission;
        }

        return $value;
    }

    public function getTotalVendorCommissionAttribute()
    {
        $vendor_value = $this->vendor_commission;

        $total = $this->grand_total;

        return floatval(number_format($total * ($vendor_value / 100), 2));
    }

    public function show_source()
    {
        if ('Agent' == $this->source_type || 'User' == $this->source_type) {
            return '<span class="tippy" data-tippy-content="'.$this->user->name.'">'.$this->source_type.'</span>';
        }

        return 'Guest';
    }

    public function getBookingStatusAttribute()
    {
        if ('DRAFT' == $this->status || 'EXPIRED' == $this->status || 'CANCELLED' == $this->status || 'RESERVED' == $this->status || 'ABANDONED' == $this->status) {
            return $this->status;
        }

        return $this->payment->status;
    }

    public function getStatusBadgeAttribute()
    {
        $css = '';

        $status = (
            'DRAFT' == $this->status || 'EXPIRED' == $this->status || 'CANCELLED' == $this->status || 'PENDING' == $this->status || 'ABANDONED' == $this->status || 'RESERVED' == $this->status
        ) ? $this->status : $this->payment?->status;

        switch ($status) {
            case 'PARTIAL':
                $css = 'badge-warning';
                break;

            case 'PENDING':
                $css = 'bg-blue';
                break;

            case 'DUE':
                $css = 'badge-danger';
                break;

            case 'DRAFT':
                $css = 'bg-grey-400';
                break;

            case 'EXPIRED':
                $css = 'alpha-danger text-grey-400';
                break;

            case 'COMPLETED':
                $css = 'bg-success';
                break;

            case 'RESERVED':
                $css = 'bg-teal';
                break;

            case 'CANCELLED':
                $css = 'bg-danger';
                break;

            case 'ABANDONED':
                $css = 'alpha-grey text-grey-300';
                break;
        }

        return $css;
    }

    public function getAllDrivers()
    {
        $guests = $this->guests;
        if ($guests) {
            $names = [];
            foreach ($guests as $guest) {
                $note = $guest->driver ? '. '.$guest->driver->notes : '';
                $driver = $guest->driver ? $guest->driver->details->name.$note : '';
                if ('' != $driver) {
                    array_push($names, $driver);
                }
            }

            return implode('<br />', $names);
        }

        return false;
    }

    public function getAllRoomsName($role = 1)
    {
        $rooms = $this->rooms;
        if ($rooms) {
            $total = $this->rooms_count;
            $names = [];
            if ($total > 0) {
                foreach ($rooms as $room) {
                    $room_name = 4 == $role ? $room->subroom->agent_name : $room->room->name .': '. $room->subroom->name;
                    array_push($names, $room_name);
                }
            }

            return implode(', ', $names);
        }

        return false;
    }

    public function showAllAddons()
    {
        $rooms = $this->rooms;
        if ($rooms) {
            $total = $this->rooms_count;
            $names = [];
            if ($total > 0) {
                foreach ($rooms as $room) {
                    $addons = [];
                    $addons_text = '';

                    if (count($room->addons) > 0) {
                        foreach ($room->addons as $addon) {
                            array_push($addons, $addon->details->name);
                        }

                        $addons_text = ': '.implode(', ', $addons);

                        if ($room->mainGuest) {
                            array_push($names, $room->mainGuest->details->details->full_name.$addons_text);
                        }
                    }
                }
            }

            if (count($names) > 0) {
                $text = implode('<br />', $names);

                return '<span class="tippy" data-tippy-content="'.$text.'"><b>Addons</b></span>';
            }
        }

        return '--';
    }

    public function getTotalAddonsPriceAttribute()
    {
        return $this->totalAddonsPrice($this->rooms, only_applicable_tax: false);
    }

    public function getTotalAddonsPriceWithApplicableTaxAttribute()
    {
        return $this->totalAddonsPrice($this->rooms, only_applicable_tax: true);
    }

    public function getSubtotalAttribute()
    {
        $rooms = $this->rooms;

        $total = 0;
        $discount = 0;

        $total = $rooms->sum(function ($room) {
            $discounts = $room->discounts;
            $discount = $discounts->sum(function ($disc) {
                return $disc->discount_value;
            });

            return $room->price - $room->duration_discount - floatVal(number_format($discount, 2));
        });

        return $total - $discount;
    }

    public function getSubtotalWithDiscountAttribute()
    {
        $rooms = $this->rooms;

        $room_price = 0;
        $total_discount = 0;

        $room_price = $rooms->sum(fn ($room) => $room->price);
        $discounts = $this->discounts;

        if ($discounts) {
            foreach ($discounts as $discount) {
                if ('Percent' == $discount->type) {
                    if ('ROOM' == $discount->apply_to) {
                        $total_discount += floatval(number_format($room_price * ($discount->value / 100), 2));
                    } else {
                        $total_discount += floatval(number_format($room_price * ($discount->value / 100), 2));
                    }
                } else {
                    $total_discount += $discount->value;
                }
            }
        }

        return $room_price - $total_discount;
    }

    public function getTotalDurationDiscountAttribute()
    {
        $rooms = $this->rooms;

        $total = 0;

        return $rooms->sum(function ($room) {
            return $room->duration_discount;
        });
    }

    public function getTotalPriceAttribute()
    {
        $room_price = $this->subtotal;
        $addons_price = $this->total_addons_price;
        $transfers = $this->total_transfers;

        return $room_price + $addons_price + $transfers;
    }

    public function getRoomTaxAttribute(): float
    {
        if (! $this->location->has('taxes') || is_null($this->location->taxes->first())) {
            return (float) 0;
        }

        return TaxService::calculateExclusiveTax($this->subtotal_with_discount, $this->location->taxes->first()->tax->rate, $this->location->taxes->first()->tax->type);
    }

    public function getGrandTotalAttribute()
    {
        // room and duration discount total
        $room_price = $this->subtotal;

        // addons price
        $addons_price = $this->total_addons_price;

        // booking transfers
        $transfers = $this->total_transfers;

        // count discount here
        $discounts = $this->discounts;

        $room_tax = $this->room_tax;

        $total_discount = 0;

        $total_price = $room_price + $room_tax + $addons_price + $transfers;

        if ($discounts) {
            foreach ($discounts as $discount) {
                if ('Percent' == $discount->type) {
                    if ('ROOM' == $discount->apply_to) {
                        $total_discount += floatval(number_format($room_price * ($discount->value / 100), 2));
                    } else {
                        $total_discount += floatval(number_format($total_price * ($discount->value / 100), 2));
                    }
                } else {
                    $total_discount += $discount->value;
                }
            }
        }

        return $total_price - $total_discount;
    }

    public function getDepositAmountAttribute()
    {
        $total = $this->grand_total;
        $location = $this->location;
        $dp_value = $location->deposit_value;
        $dp_type = $location->deposit_type;

        if ('percent' == $dp_type) {
            return floatval(number_format($total * ($dp_value / 100), 2));
        }
        if ('fixed' == $dp_type) {
            return floatval($dp_value);
        }
    }

    protected function totalAddonsPrice($rooms, bool $only_applicable_tax = false)
    {
        $addons_price = 0;
        $is_agent = auth()->check() && auth()->user()->hasRole('Agent');

        return $rooms->sum(function ($room) {
            $addons = $room->addons;

            if (!$addons) {
                return 0;
            }

            return $addons->sum(callback: fn ($addon) => $addon->price);
        });
    }

    protected function totalDurationDiscountPrice($rooms)
    {
        return $rooms->sum(function ($room) {
            return $room->duration_discount;
        });
    }
}
