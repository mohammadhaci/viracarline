<?php

namespace App\Support;

final class SwissMoney
{
    /**
     * Swiss-style CHF formatting with apostrophe thousands separator,
     * e.g. "CHF 125'000.00". Implemented with number_format instead of
     * NumberFormatter('de_CH') because ICU emits U+2019 (') as the group
     * separator, which varies across ICU versions and breaks comparisons.
     */
    public static function format(int|float|string $amount): string
    {
        return 'CHF '.number_format((float) $amount, 2, '.', "'");
    }
}
