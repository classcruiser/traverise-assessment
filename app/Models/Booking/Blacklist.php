<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Blacklist extends Model
{
    use BelongsToTenant;
    
    protected $guarded = [];
}
