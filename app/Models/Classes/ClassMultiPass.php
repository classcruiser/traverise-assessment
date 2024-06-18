<?php

namespace App\Models\Classes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ClassMultiPass extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'code_generated_at' => 'datetime:Y-m-d'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function ($passes) {
            if ($passes->isDirty('code') && $passes->type === 'VOUCHER') {
                $passes->code_generated_at = now();
            }
        });
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id', 'id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ClassBooking::class, 'class_multi_passes_id', 'id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassMultiPassSession::class, 'class_multi_pass_id', 'id');
    }

    public function getUsageAttribute()
    {
        $usage = 0;

        if ($this->bookings) {
            switch ($this->type) {
                case 'SESSION':
                    if ($this->sessions) {
                        $session_ids = $this->sessions->pluck('class_session_id')->all();
                        foreach ($this->bookings as $booking) {
                            $usage += $booking->guests->whereIn('class_session_id', $session_ids)->count();
                        }
                    }

                    break;

                default:
                    $usage += $this->bookings->count();
                    break;
            }
        }

        return $usage;
    }
}
