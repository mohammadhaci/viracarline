<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Partner;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCost;
use App\Services\PartnerAmountService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'staging', 'testing')) {
            return;
        }

        Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '100000.00');
        Setting::set(PartnerAmountService::NOTE_KEY, 'Stand: Juni 2026');

        $partners = $this->seedPartners();
        $this->seedVehicles($partners);
        Customer::factory(15)->create();
    }

    /**
     * @return Collection<int, Partner>
     */
    private function seedPartners()
    {
        // Link the demo partner login to a partner record so /partner has data.
        $demoUser = User::where('email', 'partner@vira.test')->first();

        if ($demoUser && $demoUser->partner === null) {
            Partner::factory()
                ->for($demoUser)
                ->create(['company_name' => 'Demo Partner AG']);
        }

        $partners = Partner::factory(2)->create();
        $partners->push(Partner::factory()->withOverride('175000.00', 'Inkl. Sondertranche Q2')->create());

        foreach ($partners as $partner) {
            $partner->user->assignRole('partner');
        }

        return Partner::all();
    }

    /**
     * @param  Collection<int, Partner>  $partners
     */
    private function seedVehicles($partners): void
    {
        $vehicles = collect()
            ->merge(Vehicle::factory(6)->create())
            ->merge(Vehicle::factory(4)->inWorkshop()->create())
            ->merge(Vehicle::factory(8)->listed()->create())
            ->merge(Vehicle::factory(6)->sold()->create());

        foreach ($vehicles as $i => $vehicle) {
            if ($partners->isNotEmpty() && $i % 2 === 0) {
                $vehicle->update(['partner_id' => $partners->random()->id]);
            }

            if (in_array($vehicle->status->value, ['in_workshop', 'sold'], true)) {
                VehicleCost::factory(fake()->numberBetween(1, 3))->for($vehicle)->create();
            }
        }
    }
}
