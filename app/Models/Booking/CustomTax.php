<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CustomTax extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $guarded = [];

    public function settings()
    {
        return $this->hasMany(CustomTaxSetting::class);
    }

    public function accommodations()
    {
        return $this->settings->filter(fn ($setting) => $setting->model_path === Location::class);
    }

    public function rooms()
    {
        return $this->settings->filter(fn ($setting) => $setting->model_path === Room::class);
    }

    public function addons()
    {
        return $this->settings->filter(fn ($setting) => $setting->model_path === Extra::class);
    }

    public function transfers()
    {
        return $this->settings->filter(fn ($setting) => $setting->model_path === TransferExtra::class);
    }
}
