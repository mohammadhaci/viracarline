<?php

use App\Models\Partner;
use App\Models\Setting;
use App\Services\PartnerAmountService;
use Spatie\Activitylog\Models\Activity;

it('audits changes to a partner display amount override with old and new values', function () {
    $partner = Partner::factory()->create();

    $partner->update(['display_amount_override' => '99000.00']);

    $activity = Activity::query()
        ->where('subject_type', Partner::class)
        ->where('subject_id', $partner->id)
        ->where('event', 'updated')
        ->latest('id')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->properties['old']['display_amount_override'])->toBeNull()
        ->and($activity->properties['attributes']['display_amount_override'])->toBe('99000.00');
});

it('audits changes to settings values', function () {
    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '100000.00');
    Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, '120000.00');

    $activity = Activity::query()
        ->where('subject_type', Setting::class)
        ->where('event', 'updated')
        ->latest('id')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->properties['old']['value'])->toBe('100000.00')
        ->and($activity->properties['attributes']['value'])->toBe('120000.00');
});
