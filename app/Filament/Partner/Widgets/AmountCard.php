<?php

namespace App\Filament\Partner\Widgets;

use App\Services\PartnerAmountService;
use Filament\Widgets\Widget;

/**
 * The CHF hero card — the first thing every partner sees (plan §3.4).
 * Always on top (lowest sort), full width, non-dismissable.
 */
class AmountCard extends Widget
{
    protected string $view = 'filament.partner.widgets.amount-card';

    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $partner = auth()->user()->partner;
        $service = app(PartnerAmountService::class);

        return [
            'amount' => $partner ? $service->formattedAmountFor($partner) : null,
            'note' => $partner ? $service->effectiveNoteFor($partner) : null,
        ];
    }
}
