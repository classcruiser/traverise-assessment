<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CountryComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $countries = Cache::remember('countries', 3600, function () {
            return DB::table('country_codes')->orderBy('country_name', 'asc')->get();
        });

        $view->with('countries', $countries);
    }
}
