<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\Setting;
use App\Support\SwissMoney;

/**
 * Single source of truth for the CHF amount shown on a partner's dashboard:
 * per-partner override if set, otherwise the GM-controlled global default.
 */
class PartnerAmountService
{
    public const DEFAULT_AMOUNT_KEY = 'partner_display_amount_default';

    public const NOTE_KEY = 'partner_amount_note';

    /**
     * @return string decimal string, e.g. "125000.00"
     */
    public function effectiveAmountFor(Partner $partner): string
    {
        return $partner->display_amount_override
            ?? (string) Setting::get(self::DEFAULT_AMOUNT_KEY, '0.00');
    }

    public function effectiveNoteFor(Partner $partner): ?string
    {
        return $partner->override_note ?? Setting::get(self::NOTE_KEY);
    }

    /**
     * @return string e.g. "CHF 125'000.00"
     */
    public function formattedAmountFor(Partner $partner): string
    {
        return SwissMoney::format($this->effectiveAmountFor($partner));
    }
}
