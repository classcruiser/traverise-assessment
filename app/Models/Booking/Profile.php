<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Profile extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

    protected static function newFactory(): Factory
    {
        return \Database\Factories\ProfileFactory::new();
    }
}
