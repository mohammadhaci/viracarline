<?php

use App\Filament\Manage\Pages\PartnerAmounts;
use App\Filament\Manage\Widgets\ManageStatsOverview;
use App\Models\Partner;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\PartnerAmountService;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->gm = User::factory()->create(['is_active' => true]);
    $this->gm->assignRole('gm');
});

it('serves the manage panel pages to the GM', function (string $path) {
    $this->actingAs($this->gm)
        ->get($path)
        ->assertOk();
})->with([
    'dashboard' => ['/manage'],
    'partner amounts' => ['/manage/partner-amounts'],
    'partners' => ['/manage/partners'],
    'vehicles (trading overview)' => ['/manage/vehicles'],
]);

it('shows KPI stats on the manage dashboard', function () {
    Vehicle::factory()->listed()->create(['purchase_price' => '20000.00']);
    Partner::factory()->withOverride('175000.00')->create();

    $this->actingAs($this->gm);
    Filament::setCurrentPanel('manage');

    Livewire::test(ManageStatsOverview::class)
        ->assertSee('Fahrzeuge an Lager')
        ->assertSee("CHF 20'000.00")
        ->assertSee('Partner-Kapital')
        ->assertSee("CHF 175'000.00");
});

it('lets the GM update the global default amount with an audit trail', function () {
    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '100000.00');

    $this->actingAs($this->gm);
    Filament::setCurrentPanel('manage');

    Livewire::test(PartnerAmounts::class)
        ->set('data.default_amount', '120000.00')
        ->set('data.note', 'Stand: Juli 2026')
        ->call('save')
        ->assertNotified();

    expect(Setting::get(PartnerAmountService::DEFAULT_AMOUNT_KEY))->toBe('120000.00')
        ->and(Setting::get(PartnerAmountService::NOTE_KEY))->toBe('Stand: Juli 2026')
        ->and(Activity::where('subject_type', Setting::class)->where('event', 'updated')->exists())->toBeTrue();
});

it('shows the effective amount per partner on the partner amounts page', function () {
    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '100000.00');
    $default = Partner::factory()->create(['company_name' => 'Standard AG']);
    $custom = Partner::factory()->withOverride('175000.00')->create(['company_name' => 'Spezial AG']);

    $this->actingAs($this->gm);
    Filament::setCurrentPanel('manage');

    Livewire::test(PartnerAmounts::class)
        ->assertSee('Standard AG')
        ->assertSee("CHF 100'000.00")
        ->assertSee('Spezial AG')
        ->assertSee("CHF 175'000.00");
});

it('audits per-partner override changes from the inline table', function () {
    $partner = Partner::factory()->create();

    $this->actingAs($this->gm);

    $partner->update(['display_amount_override' => '150000.00']);

    $activity = Activity::query()
        ->where('subject_type', Partner::class)
        ->where('subject_id', $partner->id)
        ->where('event', 'updated')
        ->latest('id')
        ->first();

    expect($activity->causer_id)->toBe($this->gm->id)
        ->and($activity->properties['attributes']['display_amount_override'])->toBe('150000.00');
});

it('blocks other roles from the manage panel', function (string $role) {
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    $this->actingAs($user)
        ->get('/manage/partner-amounts')
        ->assertForbidden();
})->with(['admin', 'mechanic', 'partner', 'accountant']);
