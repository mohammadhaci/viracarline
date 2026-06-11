<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

/**
 * role => panel path, mirroring User::PANEL_ROLE_MAP.
 */
$panels = [
    'admin' => '/admin',
    'gm' => '/manage',
    'mechanic' => '/workshop',
    'partner' => '/partner',
    'accountant' => '/finance',
];

$correctPairs = [];
$wrongPairs = [];

foreach ($panels as $role => $path) {
    $correctPairs["{$role} on {$path}"] = [$role, $path];

    foreach ($panels as $otherRole => $otherPath) {
        if ($otherRole !== $role) {
            $wrongPairs["{$otherRole} on {$path}"] = [$otherRole, $path];
        }
    }
}

it('allows the correct role to access its own panel', function (string $role, string $path) {
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    $this->actingAs($user)
        ->get($path)
        ->assertOk();
})->with($correctPairs);

it('blocks every other role from the panel', function (string $role, string $path) {
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    $this->actingAs($user)
        ->get($path)
        ->assertForbidden();
})->with($wrongPairs);

it('redirects guests to the panel login page', function (string $path) {
    $this->get($path)
        ->assertRedirect("{$path}/login");
})->with(array_combine(array_values($panels), array_map(fn ($p) => [$p], $panels)));

it('blocks inactive users everywhere, even with the correct role', function (string $role, string $path) {
    $user = User::factory()->create(['is_active' => false]);
    $user->assignRole($role);

    $this->actingAs($user)
        ->get($path)
        ->assertForbidden();
})->with($correctPairs);

it('blocks users without any role from every panel', function (string $path) {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user)
        ->get($path)
        ->assertForbidden();
})->with(array_combine(array_values($panels), array_map(fn ($p) => [$p], $panels)));
