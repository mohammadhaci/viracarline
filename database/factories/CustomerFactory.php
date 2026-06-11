<?php

namespace Database\Factories;

use App\Enums\CustomerType;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(CustomerType::cases()),
            'name' => fake()->name(),
            'contact' => [
                'email' => fake()->safeEmail(),
                'phone' => fake()->phoneNumber(),
            ],
            'address' => fake()->address(),
            'language' => fake()->randomElement(['de', 'de', 'de', 'fr', 'en']),
        ];
    }
}
