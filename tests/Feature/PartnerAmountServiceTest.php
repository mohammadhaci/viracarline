<?php

use App\Models\Partner;
use App\Models\Setting;
use App\Services\PartnerAmountService;

beforeEach(function () {
    $this->service = new PartnerAmountService;
});

it('falls back to the global default when the partner has no override', function () {
    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '100000.00');
    $partner = Partner::factory()->create();

    expect($this->service->effectiveAmountFor($partner))->toBe('100000.00');
});

it('prefers the per-partner override over the global default', function () {
    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '100000.00');
    $partner = Partner::factory()->withOverride('175000.00')->create();

    expect($this->service->effectiveAmountFor($partner))->toBe('175000.00');
});

it('returns zero when neither override nor default exists', function () {
    $partner = Partner::factory()->create();

    expect($this->service->effectiveAmountFor($partner))->toBe('0.00')
        ->and($this->service->formattedAmountFor($partner))->toBe('CHF 0.00');
});

it('formats the effective amount in Swiss style', function () {
    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '125000.00');
    $partner = Partner::factory()->create();

    expect($this->service->formattedAmountFor($partner))->toBe("CHF 125'000.00");
});

it('reflects a changed global default immediately for all partners without override', function () {
    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '100000.00');
    $partner = Partner::factory()->create();

    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '110000.00');

    expect($this->service->effectiveAmountFor($partner))->toBe('110000.00');
});

it('resolves the note from the override note first, then the global note', function () {
    Setting::set(PartnerAmountService::NOTE_KEY, 'Stand: Juni 2026');

    $plain = Partner::factory()->create();
    $custom = Partner::factory()->withOverride('150000.00', 'Inkl. Sondertranche')->create();

    expect($this->service->effectiveNoteFor($plain))->toBe('Stand: Juni 2026')
        ->and($this->service->effectiveNoteFor($custom))->toBe('Inkl. Sondertranche');
});
