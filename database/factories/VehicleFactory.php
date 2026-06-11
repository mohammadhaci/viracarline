<?php

namespace Database\Factories;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /** @var array<string, list<string>> */
    private const CATALOG = [
        'BMW' => ['320d', '530i xDrive', 'X3 30d'],
        'Mercedes-Benz' => ['C 220 d', 'E 300', 'GLC 250 4MATIC'],
        'Audi' => ['A4 40 TFSI', 'A6 45 TDI quattro', 'Q5 50 TDI'],
        'VW' => ['Golf 1.5 TSI', 'Passat 2.0 TDI', 'Tiguan 2.0 TSI'],
        'Škoda' => ['Octavia 2.0 TDI', 'Superb 1.8 TSI'],
        'Toyota' => ['Corolla 1.8 Hybrid', 'RAV4 2.5 Hybrid'],
    ];

    public function definition(): array
    {
        $brand = fake()->randomElement(array_keys(self::CATALOG));
        $model = fake()->randomElement(self::CATALOG[$brand]);
        $purchasePrice = fake()->numberBetween(50, 600) * 100;
        $title = sprintf('%s %s (%d)', $brand, $model, $year = fake()->numberBetween(2012, 2025));

        return [
            'vin' => fake()->unique()->regexify('[A-HJ-NPR-Z0-9]{17}'),
            'brand' => $brand,
            'model' => $model,
            'variant' => fake()->optional()->randomElement(['Sport', 'Style', 'Premium', 'Business']),
            'year' => $year,
            'mileage_km' => fake()->numberBetween(5_000, 220_000),
            'fuel' => fake()->randomElement(['petrol', 'diesel', 'hybrid', 'electric']),
            'transmission' => fake()->randomElement(['manual', 'automatic']),
            'color' => fake()->safeColorName(),
            'purchase_price' => number_format($purchasePrice, 2, '.', ''),
            'purchase_date' => fake()->dateTimeBetween('-1 year'),
            'purchase_source' => fake()->randomElement(['private', 'dealer', 'auction']),
            'asking_price' => number_format(round($purchasePrice * fake()->randomFloat(2, 1.15, 1.40), -2), 2, '.', ''),
            'sold_price' => null,
            'sold_at' => null,
            'status' => VehicleStatus::Purchased,
            'partner_id' => null,
            'is_published' => false,
            'is_featured' => false,
            'show_price' => true,
            'title' => ['de' => $title, 'fr' => $title, 'en' => $title],
            'description' => null,
            'slug' => Str::slug("{$brand} {$model} {$year}").'-'.Str::lower(Str::random(6)),
        ];
    }

    public function inWorkshop(): static
    {
        return $this->state(fn () => ['status' => VehicleStatus::InWorkshop]);
    }

    public function listed(): static
    {
        return $this->state(fn () => [
            'status' => VehicleStatus::Listed,
            'is_published' => true,
        ]);
    }

    public function sold(): static
    {
        return $this->state(function (array $attributes) {
            $soldAt = fake()->dateTimeBetween($attributes['purchase_date'] ?? '-6 months');

            return [
                'status' => VehicleStatus::Sold,
                'sold_price' => number_format(round(((float) $attributes['purchase_price']) * fake()->randomFloat(2, 1.05, 1.35), -2), 2, '.', ''),
                'sold_at' => $soldAt,
            ];
        });
    }
}
