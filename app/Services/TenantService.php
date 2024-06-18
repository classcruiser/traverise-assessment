<?php

namespace App\Services;

use App\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;

class TenantService
{
    public static function isTenantExist(string $id): bool
    {
        $check = Tenant::whereId($id)->count();

        if ($check) {
            throw new \Exception('Tenant already exist');
        }

        return false;
    }

    public static function isTenantActive(string $id): bool
    {
        $domain = Domain::where('domain', $id)->first();
        $check = Tenant::whereId($domain->tenant_id)->where('is_active', 1)->count();

        return $check > 0;
    }
}
