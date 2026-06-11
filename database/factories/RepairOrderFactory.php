<?php

namespace Database\Factories;

use App\Enums\RepairOrderPriority;
use App\Enums\RepairOrderStatus;
use App\Enums\RepairOrderType;
use App\Models\Customer;
use App\Models\RepairOrder;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RepairOrder>
 */
class RepairOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => RepairOrderType::Internal,
            'vehicle_id' => Vehicle::factory(),
            'customer_id' => null,
            'assigned_to' => null,
            'status' => RepairOrderStatus::Open,
            'priority' => fake()->randomElement(RepairOrderPriority::cases()),
            'diagnosis' => fake()->sentence(8),
        ];
    }

    public function customer(): static
    {
        return $this->state(fn () => [
            'type' => RepairOrderType::Customer,
            'vehicle_id' => null,
            'customer_id' => Customer::factory(),
            'customer_vehicle_info' => [
                'brand' => 'VW Golf',
                'plate' => 'ZH '.fake()->numberBetween(10000, 999999),
            ],
        ]);
    }
}
