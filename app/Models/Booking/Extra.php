<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Extra extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    public function prices()
    {
        return $this->hasMany(PriceExtra::class);
    }

    public function rooms()
    {
        return $this->hasMany(RoomExtra::class);
    }

    public function questionnaire()
    {
        return $this->hasOne(Questionnaire::class, 'id', 'questionnaire_id');
    }

    public function taxes()
    {
        return $this
            ->hasMany(CustomTaxSetting::class, 'model_id', 'id')
            ->where('model_path', self::class);
    }
}
