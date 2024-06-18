<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class SurfPlannerUser extends Model
{
    protected $guarded = [];
    
    protected $table = 'surf_planner_users';

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
