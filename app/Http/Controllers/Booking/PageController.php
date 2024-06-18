<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;

use App\Models\Booking\AgentRoom;
use App\Models\Booking\Location;
use App\Models\Booking\User;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function thankYou()
    {
        return view('Booking.payments.thank-you');
    }
}
