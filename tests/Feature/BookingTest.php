<?php

use App\Models\Tenant;
use App\Models\Booking\Room;
use App\Models\Booking\User;
use App\Models\Booking\Guest;
use App\Models\Booking\Booking;
use App\Models\Booking\Location;
use App\Models\Booking\RoomInfo;
use Illuminate\Support\Str;
use App\Models\Booking\BookingRoomGuest;
use App\Models\Booking\EmailTemplate;
use App\Models\Booking\Profile;
use App\Services\Booking\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Booking\Bookings\BookingGeneralService;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->profile = Profile::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);

    $this->sales = User::factory()->create([
        'tenant_id' => tenant('id'),
        'email' => 'sales@test.com',
        'name' => 'sales',
        'password' => bcrypt('sales'),
    ]);

    $this->location = Location::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);
    $this->room = Room::factory()->create([
        'tenant_id' => $this->tenant->id,
        'location_id' => $this->location->id,
    ]);
    $this->subroom = RoomInfo::factory()->create([
        'room_id' => $this->room->id,
    ]);
});


test('guest can open book now page', function () {
    $this->get(tenant_route($this->domain, 'booknow.index'))->assertStatus(200);
});


test('guest can select location and move to room page', function () {
    $response = $this
        ->post(tenant_route($this->domain, 'booknow.process-location'), [
            'location_id' => $this->location->id,
        ]);

    $response->assertRedirect(tenant_route($this->domain, 'booknow.select-room'));
});

test('guest can select room and move to addons page', function () {
    $response = $this
        ->withSession([
            'location' => $this->location->id,
            'booking_date_start' => now()->format('Y-m-d'),
            'booking_date_end' => now()->addDays(1)->format('Y-m-d'),
            'room' => [],
        ])
        ->post(tenant_route($this->domain, 'booknow.process-room'), [
            'room_id' => $this->room->id,
            'bed_type' => 'Double',
            'room_type' => 'Shared',
            'private_booking' => 0,
            'guest' => 1,
        ]);

    $response->assertRedirect(tenant_route($this->domain, 'booknow.select-addons'));
});

test('guest can open guest details page', function () {
    $response = $this
        ->withSession([
            'location' => $this->location->id,
            'booking_date_start' => now()->format('Y-m-d'),
            'booking_date_end' => now()->addDays(1)->format('Y-m-d'),
            'room' => [
                'key' => uniqid(),
                'name' => $this->room->name,
                'room_id' => $this->room->id,
                'featured_image' => $this->room->featured_image,
                'location_id' => $this->location->id,
                'guest' => 1,
                'room_type' => 'Shared',
                'private_booking' => 0,
                'empty_fee' => 0,
                'total_empty_fee' => 0,
                'bed_type' => 'Double',
                'tax' => 0,
                'other_tax' => [
                    'hotel_tax' => 0,
                    'goods_tax' => 0,
                ],
                'tax_info' => [
                    'cultural_tax' => 0,
                    'hotel_tax' => 0,
                    'goods_tax' => 0,
                ],
                'accommodation_price' => 100,
                'price' => 100,
                'duration' => 1,
                'duration_discount' => 0,
                'availability_status' => 1,
                'is_available' => 1,
                'addons_key' => null,
                'addons' => collect([]),
                'open_spot' => 0,
                'transfers_key' => Str::random(6),
                'transfers' => collect([]),
                'inclusions' => null,
                'voucher' => '',
                'voucher_detail' => null,
                'special_offer' => null,
                'offer_discount' => 0,
                'comment' => '',
            ],
        ])
        ->get(tenant_route($this->domain, 'booknow.guest-details'));

    $response->assertStatus(200);
});


test('guest can confirm the booking', function () {
    $booking_ref = (new BookingGeneralService)->generateBookingRef();
    $email = fake()->email;

    $guest = Guest::factory()->create([
        'tenant_id' => $this->tenant->id,
        'email' => $email,
    ]);
    $booking = Booking::factory()->create([
        'ref' => $booking_ref,
        'tenant_id' => $this->tenant->id,
        'location_id' => $this->location->id,
        'status' => 'RESERVED',
    ]);

    $booking_guest = $booking->guest()->create([
        'guest_id' => $guest->id,
        'booking_id' => $booking->id,
        'group_id' => 0,
    ]);

    $booking_room = $booking->rooms()->create([
        'room_id' => $this->room->id,
        'subroom_id' => $this->subroom->id,
        'bed' => 1,
        'bed_type' => 'Double',
        'bathroom' => 'Shared',
        'from' => $booking->check_in->format('Y-m-d'),
        'to' => $booking->check_out->format('Y-m-d'),
        'is_private' => 0,
        'guest' => 1,
        'price' => 200,
        'duration_discount' => 0,
    ]);

    (new PaymentService)->createPayment($booking);

    $br_guest = BookingRoomGuest::create([
        'booking_room_id' => $booking_room->id,
        'booking_guest_id' => $booking_guest->id,
    ]);

    $sessions = [
        'room' => [
            'key' => uniqid(),
            'name' => $this->room->name,
            'room_id' => $this->room->id,
            'guest' => 1,
            'room_type' => $this->room->room_type,
            'private_booking' => false,
            'bed_type' => json_decode($this->room->bed_type, true)[0],
            'availability_status' => 1,
            'is_available' => true,
            'open_spot' => 1,
            'voucher' => '',
            'promo' => '',
            'special_offer' => null,
            'addons' => collect([]),
            'transfers' => collect([]),
            'comment' => 'From Test suite',
        ],
        'booking_ref' => $booking_ref,
        'booking_guest' => $booking->guest->full_name,
        'location' => $this->location->id,
        'booking_date_start' => $booking->check_in->format('Y-m-d'),
        'booking_date_end' => $booking->check_out->format('Y-m-d'),
        'guest' => 1,
    ];

    $response = $this->withSession($sessions)
        ->followingRedirects()
        ->post(tenant_route($this->domain, 'booknow.process-confirm'))
        ->assertStatus(200);

    $this->assertDatabaseHas('bookings', ['ref' => $booking_ref]);
    $this->assertDatabaseHas('guests', ['id' => $guest->id, 'email' => $email]);
    $this->assertDatabaseHas('booking_guests', ['id' => $booking_guest->id]);
    $this->assertDatabaseHas('booking_rooms', ['id' => $booking_room->id]);
    $this->assertDatabaseHas('booking_room_guests', ['id' => $br_guest->id]);
});
