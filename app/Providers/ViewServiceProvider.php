<?php

namespace App\Providers;

use App\View\Composers\BooknowComposer;
use App\View\Composers\CountryComposer;
use App\View\Composers\LocationComposer;
use App\View\Composers\HeaderComposer;
use App\View\Composers\SidebarComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register()
    {
        // ...
    }

    public function boot()
    {
        View::composer('Booking.partials.header', HeaderComposer::class);
        View::composer('Booking.bookings.sidebar', SidebarComposer::class);
        View::composer('Booking.booking', BooknowComposer::class);
        View::composer('Booking.book-package', BooknowComposer::class);
        View::composer('Booking.document', BooknowComposer::class);
        View::composer('Booking.main', BooknowComposer::class);
        View::composer(
            [
                'Classes.booking.index',
                'Classes.booking.trash',
                'Classes.multi-pass.orders',
                'Classes.guest.index',
                'Classes.guest.edit'
            ],
            CountryComposer::class
        );
        View::composer(
            ['Classes.booking.index', 'Classes.multi-pass.orders'],
            LocationComposer::class
        );
    }
}
