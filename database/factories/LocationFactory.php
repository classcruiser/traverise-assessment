<?php

namespace Database\Factories;

use App\Models\Booking\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'short_name' => $this->faker->name,
            'abbr' => $this->faker->name,
            'address' => $this->faker->address,
            'description' => $this->faker->text,
            'contact_email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'details' => $this->faker->text,
            'terms' => $this->faker->text,
            'price_type' => 'guest',
            'title' => $this->faker->name,
            'subtitle' => $this->faker->name,
            'allow_pending' => 1,
            'duration_discount' => 1,
            'min_discount' => 1,
            'max_discount' => 50,
            'enable_deposit' => 1,
            'deposit_type' => 'percent',
            'deposit_value' => 50,
            'deposit_due' => 7,
            'color' => null,
            'service' => null,
            'minimum_checkin' => null,
            'minimum_nights' => 1,
            'maximum_nights' => null,
            'admin_visible' => 1,
            'cultural_tax' => 10,
            'hotel_tax' => 17,
            'goods_tax' => 20,
            'bank_transfer' => 1,
            'bank_transfer_text' => $this->faker->text,
            'active' => 1,
            'has_arrival_rule' => 0,
        ];
    }
}
