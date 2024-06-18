<?php

namespace App\View\Composers;

use App\Models\Booking\Location;
use Illuminate\View\View;

class LocationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $locations = Location::with(['rooms'])->orderBy('name', 'asc')->get();

        $view->with('locations', $locations);
    }
}
