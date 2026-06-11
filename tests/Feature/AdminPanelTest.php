<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->assignRole('admin');
});

it('serves the admin CMS resource pages', function (string $path) {
    $this->actingAs($this->admin)
        ->get($path)
        ->assertOk();
})->with([
    'pages' => ['/admin/pages'],
    'menus' => ['/admin/menus'],
    'posts' => ['/admin/posts'],
    'post categories' => ['/admin/post-categories'],
    'leads' => ['/admin/leads'],
    'redirects' => ['/admin/redirects'],
    'users' => ['/admin/users'],
    'vehicles' => ['/admin/vehicles'],
    'activity log' => ['/admin/activity-logs'],
    'site settings' => ['/admin/site-settings'],
    'page create form' => ['/admin/pages/create'],
]);

it('blocks non-admin roles from the CMS', function () {
    $gm = User::factory()->create(['is_active' => true]);
    $gm->assignRole('gm');

    $this->actingAs($gm)
        ->get('/admin/pages')
        ->assertForbidden();
});
