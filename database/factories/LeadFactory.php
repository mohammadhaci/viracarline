<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => LeadType::Contact,
            'vehicle_id' => null,
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'message' => fake()->paragraph(),
            'locale' => fake()->randomElement(['de', 'de', 'fr', 'en']),
            'status' => LeadStatus::New,
            'assigned_to' => null,
        ];
    }

    public function vehicleInquiry(): static
    {
        return $this->state(fn () => ['type' => LeadType::VehicleInquiry]);
    }
}
