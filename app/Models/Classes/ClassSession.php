<?php

namespace App\Models\Classes;

use App\Models\Booking\User;
use App\Models\Traits\Activeable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ClassSession extends Model
{
    use HasFactory;
    use BelongsToTenant;
    use Activeable;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'price', 'is_active', 'max_pax', 'color', 'instructor_id', 'class_category_id'];

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class)
                    ->orderByRaw('field(day, "MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN")')
                    ->orderBy('start');
    }

    public function addons(): HasMany
    {
        return $this->hasMany(ClassSessionAddon::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClassCategory::class, 'class_category_id', 'id');
    }

    /**
     * Interact with the session's color.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function color(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::remove('#', $value),
            set: fn ($value) => Str::remove('#', $value),
        );
    }
}
