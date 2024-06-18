<?php

namespace App\Services\Booking\Bookings;

use Illuminate\Support\Str;
use App\Models\Booking\Booking;
use Illuminate\Support\Facades\DB;
use App\Models\Booking\BookingHistory;

class BookingGeneralService
{
    public static function generateBookingRef(): string
    {
        $domain = DB::table('domains')->where('tenant_id', tenant('id'))->first();

        return Str::upper(substr($domain->domain, 0, 3) .'-'. time() .'-'. Str::random(3));
    }
}