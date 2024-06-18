<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Questionnaire extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function type()
    {
        return $this->belongsTo(QuestionnaireType::class);
    }

    public function answers()
    {
        return $this->hasMany(QuestionnaireAnswer::class);
    }
}
