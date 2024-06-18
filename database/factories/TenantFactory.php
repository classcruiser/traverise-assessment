<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TenantFactory extends Factory
{
    protected $model = \App\Models\Tenant::class;
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'plan' => 'premium',
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'company' => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'country' => 'US',
            'zip' => $this->faker->postcode,
            'subscription_start' => now(),
            'subscription_end' => now()->addYear(),
            'is_active' => 1,
            'stripe_account_id' => 'acct_1MOHwnRWzh7DTn0K',
            'stripe_onboarding_process' => 1,
        ];
    }
}
