<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'staging', 'testing')) {
            return;
        }

        $password = env('DEMO_PASSWORD');

        if (blank($password)) {
            $this->command?->warn('DEMO_PASSWORD is not set — skipping demo users.');

            return;
        }

        $demoUsers = [
            ['name' => 'Demo Admin', 'email' => 'admin@vira.test', 'role' => 'admin'],
            ['name' => 'Demo GM', 'email' => 'gm@vira.test', 'role' => 'gm'],
            ['name' => 'Demo Mechanic', 'email' => 'mechanic@vira.test', 'role' => 'mechanic'],
            ['name' => 'Demo Partner', 'email' => 'partner@vira.test', 'role' => 'partner'],
            ['name' => 'Demo Accountant', 'email' => 'finance@vira.test', 'role' => 'accountant'],
        ];

        foreach ($demoUsers as $demoUser) {
            $user = User::firstOrCreate(
                ['email' => $demoUser['email']],
                [
                    'name' => $demoUser['name'],
                    'password' => Hash::make($password),
                    'is_active' => true,
                    'locale' => 'de',
                ],
            );

            $user->syncRoles([$demoUser['role']]);
        }
    }
}
