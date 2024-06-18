<?php

namespace Database\Factories;

use App\Models\Booking\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = now();

        return [
            'title' => $this->faker->title,
            'short_description' => $this->faker->paragraph(2),
            'description' => $this->faker->text,
            'address' => $this->faker->address,
            'phone_number' => $this->faker->phoneNumber,
            'contact_person' => $this->faker->name,
            'contact_email' => $this->faker->email,
            'copy_email' => null,
            'bg_color' => '#FFFFFF',
            'bg_pos_horizontal' => 'center',
            'bg_pos_vertical' => 'center',
            'primary_color' => '#FFFFFF',
            'secondary_color' => '#FFFFFF',
            'accent_color' => '#FFFFFF',
            'owner_name' => $this->faker->name,
            'owner_email' => $this->faker->email,
            'owner_phone' => $this->faker->phoneNumber,
            'ceo_name' => $this->faker->name,
            'ceo_email' => $this->faker->email,
            'ceo_phone' => $this->faker->phoneNumber,
            'vat_id' => $this->faker->randomNumber(8),
            'stripe_id' => $this->faker->randomNumber(8),
            'iban' => $this->faker->randomNumber(8),
            'commercial_register_number' => $this->faker->randomNumber(8),
            'district_court' => $this->faker->randomNumber(8),
            'stripe_fee_percentage' => $this->faker->randomNumber(2),
            'stripe_fee_fixed' => $this->faker->randomNumber(2),
            'test_mode' => 1,
            'unpaid_booking_deletion_in' => 1,
        ];
    }
}
