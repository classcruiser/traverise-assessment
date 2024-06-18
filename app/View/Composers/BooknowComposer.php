<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

use App\Models\Booking\Profile;

class BooknowComposer
{
    public function compose(View $view)
    {
        $tenant_settings = Cache::remember(tenant('id') .'_tenant_settings', 1, function () {
            return Profile::where('tenant_id', tenant('id'))->first();
        });

        $view->with('tenant_settings', $tenant_settings);
    }
}
