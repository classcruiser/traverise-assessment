<?php

namespace App\Models\Classes;

use App\Models\Booking\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ClassGuest extends Model
{
    use BelongsToTenant;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ['date', 'created_at', 'updated_at'];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'check_in_at' => 'datetime:Y-m-d H:i:s'
    ];

    protected $appends = [
        'full_name'
    ];

    public function booking()
    {
        return $this->belongsTo(ClassBooking::class, 'class_booking_id', 'id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id', 'id')->withTrashed();
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id', 'id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id', 'id');
    }

    /**
     * Get full name attribute
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function fullName(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attribute) => $attribute['first_name'] . ' ' . $attribute['last_name'],
        );
    }

    public function getAgeAttribute(): int
    {
        return (int) date_diff(date_create($this->birthdate), date_create('now'))->format('%y');
    }
}
