<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Role names are referenced in User::PANEL_ROLE_MAP and across all phases; do not rename.
     */
    public function run(): void
    {
        foreach (['admin', 'gm', 'mechanic', 'partner', 'accountant'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }
}
