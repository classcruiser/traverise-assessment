<?php

namespace Database\Factories;

use App\Models\Booking\RoomInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomInfo>
 */
class RoomInfoFactory extends Factory
{
    protected $model = RoomInfo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'beds' => 2,
        ];
    }
}
