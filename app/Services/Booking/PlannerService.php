<?php

namespace App\Services\Booking;

use Illuminate\Support\Facades\Cache;
use Ixudra\Curl\Facades\Curl;

class PlannerService
{
    public static function getLocations()
    {
        $response = Cache::remember('surfplanner_locations', 60, function () {
            return Curl::to(env('SURFPLANNER_URL') .'/api/locations')->get();
        });

        return json_decode($response);
    }

    public static function updateUser()
    {
        // ...
    }
}
