<?php

namespace App\Models\Classes;

use App\Models\Booking\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'start_formatted',
        'end_formatted',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class_session_id',
        'day',
        'date',
        'start',
        'end',
        'price',
        'max_pax',
        'instructor_id',
        'is_active',
        'weeks',
        'until'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date:Y-m-d',
        'is_active' => 'boolean',
    ];

    public function session() : BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id', 'id');
    }

    public function bookings() : HasMany
    {
        return $this->hasMany(ClassGuest::class, 'class_schedule_id', 'id');
    }

    public function instructor() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Formatted the time.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function startFormatted(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attribute) => !blank($attribute['start']) ? $this->timeFormat($attribute['start']) : null,
        );
    }

    /**
     * Formatted the time.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function endFormatted(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attribute) => !blank($attribute['end']) ? $this->timeFormat($attribute['end']) : null,
        );
    }

    public function timeFormat($time)
    {
        return substr($time, 0, 5);
    }
}
