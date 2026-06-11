<?php

use App\Filament\Partner\Widgets\AmountCard;
use App\Filament\Partner\Widgets\MyVehicles;
use App\Models\Partner;
use App\Models\Setting;
use App\Models\Vehicle;
use App\Services\PartnerAmountService;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->partner = Partner::factory()->create(['company_name' => 'Partner A AG']);
    $this->partner->user->assignRole('partner');
    $this->partnerUser = $this->partner->user;

    $this->otherPartner = Partner::factory()->create(['company_name' => 'Partner B AG']);
    $this->otherPartner->user->assignRole('partner');
});

it('serves the partner dashboard and statements page', function (string $path) {
    $this->actingAs($this->partnerUser)
        ->get($path)
        ->assertOk();
})->with([
    'dashboard' => ['/partner'],
    'statements' => ['/partner/statements'],
    'profile' => ['/partner/profile'],
]);

it('shows the prominent CHF card with the effective amount and note', function () {
    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '125000.00');
    Setting::set(PartnerAmountService::NOTE_KEY, 'Stand: Juni 2026');

    $this->actingAs($this->partnerUser);
    Filament::setCurrentPanel('partner');

    Livewire::test(AmountCard::class)
        ->assertSee("CHF 125'000.00")
        ->assertSee('Stand: Juni 2026');
});

it('shows a GM change of the global amount instantly', function () {
    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '100000.00');

    $this->actingAs($this->partnerUser);
    Filament::setCurrentPanel('partner');

    Livewire::test(AmountCard::class)->assertSee("CHF 100'000.00");

    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '110000.00');

    Livewire::test(AmountCard::class)->assertSee("CHF 110'000.00");
});

it('shows the per-partner override instead of the default when set', function () {
    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '100000.00');
    $this->partner->update(['display_amount_override' => '175000.00', 'override_note' => 'Inkl. Tranche Q2']);

    $this->actingAs($this->partnerUser);
    Filament::setCurrentPanel('partner');

    Livewire::test(AmountCard::class)
        ->assertSee("CHF 175'000.00")
        ->assertDontSee("CHF 100'000.00")
        ->assertSee('Inkl. Tranche Q2');
});

it('lists only my own vehicles (IDOR)', function () {
    Vehicle::factory()->create(['partner_id' => $this->partner->id, 'brand' => 'BMW', 'model' => 'MeinWagen']);
    Vehicle::factory()->create(['partner_id' => $this->otherPartner->id, 'brand' => 'Audi', 'model' => 'FremdWagen']);

    $this->actingAs($this->partnerUser);
    Filament::setCurrentPanel('partner');

    Livewire::test(MyVehicles::class)
        ->assertSee('MeinWagen')
        ->assertDontSee('FremdWagen');
});

it('lets a partner download their own statement via a signed url', function () {
    $media = $this->partner
        ->addMedia(UploadedFile::fake()->create('abrechnung.pdf', 100, 'application/pdf'))
        ->toMediaCollection('statements', 'local');

    $url = URL::temporarySignedRoute('partner.statements.download', now()->addMinutes(30), ['media' => $media->id]);

    $this->actingAs($this->partnerUser)
        ->get($url)
        ->assertOk()
        ->assertDownload('abrechnung.pdf');
});

it('blocks downloading another partners statement (IDOR)', function () {
    $media = $this->otherPartner
        ->addMedia(UploadedFile::fake()->create('fremd.pdf', 100, 'application/pdf'))
        ->toMediaCollection('statements', 'local');

    $url = URL::temporarySignedRoute('partner.statements.download', now()->addMinutes(30), ['media' => $media->id]);

    $this->actingAs($this->partnerUser)
        ->get($url)
        ->assertForbidden();
});

it('rejects unsigned statement download urls', function () {
    $media = $this->partner
        ->addMedia(UploadedFile::fake()->create('abrechnung.pdf', 100, 'application/pdf'))
        ->toMediaCollection('statements', 'local');

    $this->actingAs($this->partnerUser)
        ->get("/partner/statements/{$media->id}/download")
        ->assertForbidden();
});

it('never exposes other partners or override mechanics on the dashboard', function () {
    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '100000.00');
    $this->otherPartner->update(['display_amount_override' => '999999.00']);

    $this->actingAs($this->partnerUser);
    Filament::setCurrentPanel('partner');

    Livewire::test(AmountCard::class)
        ->assertDontSee('Partner B AG')
        ->assertDontSee("999'999");
});
