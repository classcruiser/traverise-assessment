<?php

namespace Database\Factories;

use App\Models\Booking\Booking;
use App\Services\Booking\Bookings\BookingGeneralService as BookingsBookingGeneralService;
use App\Services\Bookings\BookingGeneralService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = now();

        return [
            'ref' => (new BookingsBookingGeneralService)->generateBookingRef(),
            'status' => array_rand(['DRAFT', 'PENDING', 'ABANDONED', 'RESERVED', 'CONFIRMED', 'CANCELLED']),
            'affiliation_id' => null,
            'special_package_id' => null,
            'location_id' => null,
            'check_in' => $date->format('Y-m-d'),
            'check_out' => $date->addDays(7)->format('Y-m-d'),
            'channel' => 'online',
            'source_type' => 'Guest',
            'source_id' => null,
            'opportunity' => array_rand(['Sale', 'Pending']),
            'expire_at' => 24,
            'expiry' => $date->addHours(24)->format('Y-m-d H:i:s'),
            'deposit_expiry' => $date->addDays(7)->format('Y-m-d'),
            'cancel_reason' => null,
            'agent_id' => null,
            'agent_commission' => 0.00,
            'voucher' => null,
            'origin' => null,
            'is_blacklisted' => 0,
            'has_check_in' => false,
            'checked_in_at' => null,
            'notes' => null,
            'tax_visible' => 1
        ];
    }
}
