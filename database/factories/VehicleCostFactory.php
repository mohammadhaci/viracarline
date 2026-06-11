<?php

namespace Database\Factories;

use App\Enums\VehicleCostType;
use App\Models\Vehicle;
use App\Models\VehicleCost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleCost>
 */
class VehicleCostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'type' => fake()->randomElement(VehicleCostType::cases()),
            'amount' => number_format(fake()->numberBetween(1, 50) * 100, 2, '.', ''),
            'note' => fake()->optional()->sentence(4),
            'repair_order_id' => null,
        ];
    }
}
