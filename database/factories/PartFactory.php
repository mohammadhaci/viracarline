<?php

namespace Database\Factories;

use App\Models\Part;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Part>
 */
class PartFactory extends Factory
{
    public function definition(): array
    {
        $cost = fake()->numberBetween(5, 400);

        return [
            'sku' => strtoupper(Str::random(3)).'-'.fake()->unique()->numberBetween(1000, 99999),
            'name' => fake()->randomElement([
                'Bremsbeläge vorne', 'Ölfilter', 'Luftfilter', 'Zündkerze', 'Batterie 70Ah',
                'Wischerblatt', 'Bremsscheibe', 'Keilriemen', 'Innenraumfilter', 'Glühbirne H7',
            ]),
            'stock_qty' => fake()->numberBetween(0, 40),
            'min_qty' => fake()->numberBetween(2, 8),
            'cost_price' => number_format($cost, 2, '.', ''),
            'sale_price' => number_format($cost * 1.5, 2, '.', ''),
        ];
    }
}
