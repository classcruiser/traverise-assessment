<?php

namespace App\Http\Controllers;

use App\Models\Booking\Profile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getTenantProfile(): Profile | null
    {
        return Profile::where('tenant_id', tenant('id'))->first();
    }
}
