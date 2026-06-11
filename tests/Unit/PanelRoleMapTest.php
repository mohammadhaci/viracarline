<?php

use App\Models\User;

it('maps every panel to exactly one role', function () {
    expect(User::PANEL_ROLE_MAP)
        ->toHaveCount(5)
        ->toMatchArray([
            'admin' => 'admin',
            'manage' => 'gm',
            'workshop' => 'mechanic',
            'partner' => 'partner',
            'finance' => 'accountant',
        ]);
});
