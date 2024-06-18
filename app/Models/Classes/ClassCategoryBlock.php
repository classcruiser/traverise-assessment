<?php

namespace App\Models\Classes;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassCategoryBlock extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'is_active' => 'boolean',
        'is_wholeday' => 'boolean',
        'is_day_off' => 'boolean',
        'is_all_session' => 'boolean'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClassCategory::class);
    }

    /**
     * The sessions that belong to the category block.
     */
    public function sessions()
    {
        return $this->belongsToMany(ClassSession::class, 'class_category_block_sessions');
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
