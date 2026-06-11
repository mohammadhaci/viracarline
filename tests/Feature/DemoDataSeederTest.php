<?php

use App\Models\Customer;
use App\Models\Partner;
use App\Models\Setting;
use App\Models\Vehicle;
use App\Services\PartnerAmountService;
use Database\Seeders\DatabaseSeeder;

it('seeds a realistic demo dataset', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Setting::get(PartnerAmountService::DEFAULT_AMOUNT_KEY))->toBe('100000.00')
        ->and(Partner::count())->toBeGreaterThanOrEqual(3)
        ->and(Vehicle::count())->toBe(24)
        ->and(Customer::count())->toBe(15)
        ->and(Vehicle::where('status', 'sold')->whereNull('sold_price')->count())->toBe(0)
        ->and(Vehicle::whereHas('costs')->count())->toBeGreaterThan(0);

    // Every partner user carries the partner role required for /partner access.
    Partner::with('user')->get()->each(
        fn (Partner $partner) => expect($partner->user->hasRole('partner'))->toBeTrue(),
    );
});
