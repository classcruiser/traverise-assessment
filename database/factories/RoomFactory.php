<?php

namespace Database\Factories;

use App\Models\Booking\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'availability' => 'auto',
            'room_short_description' => $this->faker->text,
            'room_description' => $this->faker->text,
            'featured_image' => null,
            'inclusions' => $this->faker->text,
            'room_type' => 'Shared',
            'price_type' => 'Guest',
            'capacity' => 2,
            'bed_type' => '["Twin","Double"]',
            'bathroom_type' => 'Shared',
            'smoking' => 0,
            'default_price' => 10,
            'min_nights' => 1,
            'max_nights' => null,
            'min_guest' => 1,
            'max_guest' => null,
            'private_space' => 2,
            'allow_private' => 1,
            'force_private' => 0,
            'allow_pending' => 1,
            'progressive_pricing' => 0,
            'occupancy_surcharge' => 0,
            'empty_fee_low' => 10,
            'empty_fee_main' => 15,
            'empty_fee_peak' => 20,
            'empty_fee_special' => 25,
            'limited_threshold' => 0,
            'active' => 1,
            'admin_active' => 1,
            'calendar_visibility' => 1,
            'sort' => 1,
            'cal_sort' => 1,
        ];
    }
}
