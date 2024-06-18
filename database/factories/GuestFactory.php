<?php

namespace Database\Factories;

use App\Models\Booking\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guest>
 */
class GuestFactory extends Factory
{
    protected $model = Guest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'is_other_guest' => 0,
            'fname' => fake()->firstName,
            'lname' => fake()->lastName,
            'company' => fake()->name,
            'title' => 'Mr',
            'email' => fake()->email,
            'phone' => fake()->phoneNumber,
            'street' => fake()->streetAddress,
            'zip' => fake()->postcode,
            'city' => fake()->city,
            'country' => fake()->country,
            'birthdate' => fake()->date(),
            'marketing_flag' => 1,
            'agent_id' => null,
            'client_id' => null,
            'stripe_customer_id' => null,
            'active_class_multi_passes_id' => null,
        ];
    }
}
