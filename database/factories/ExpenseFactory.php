<?php

namespace Database\Factories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        $amount = fake()->numberBetween(50, 3000);

        return [
            'category' => fake()->randomElement(array_keys(Expense::CATEGORIES)),
            'amount' => number_format($amount, 2, '.', ''),
            'vat_amount' => number_format(round($amount * 0.081 / 1.081, 2), 2, '.', ''),
            'date' => fake()->dateTimeBetween('-3 months'),
            'vendor' => fake()->company(),
            'vehicle_id' => null,
            'note' => fake()->optional()->sentence(4),
        ];
    }
}
