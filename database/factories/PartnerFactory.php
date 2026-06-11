<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Partner>
 */
class PartnerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_name' => fake()->company(),
            'contact' => [
                'name' => fake()->name(),
                'email' => fake()->safeEmail(),
                'phone' => fake()->phoneNumber(),
            ],
            'display_amount_override' => null,
            'override_note' => null,
            'joined_at' => fake()->dateTimeBetween('-3 years', '-1 month'),
            'is_active' => true,
        ];
    }

    public function withOverride(string $amount = '150000.00', ?string $note = null): static
    {
        return $this->state(fn () => [
            'display_amount_override' => $amount,
            'override_note' => $note,
        ]);
    }
}
