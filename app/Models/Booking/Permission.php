<?php

namespace App\Models\Booking;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Permission extends SpatiePermission
{
    use BelongsToTenant;
}
