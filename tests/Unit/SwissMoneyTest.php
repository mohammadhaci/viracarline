<?php

use App\Support\SwissMoney;

it('formats amounts in Swiss style with apostrophe separators', function (int|float|string $amount, string $expected) {
    expect(SwissMoney::format($amount))->toBe($expected);
})->with([
    'large amount' => [125000, "CHF 125'000.00"],
    'decimal string' => ['125000.00', "CHF 125'000.00"],
    'millions' => [1234567.89, "CHF 1'234'567.89"],
    'small amount' => [1234.5, "CHF 1'234.50"],
    'sub-thousand' => [950, 'CHF 950.00'],
    'zero' => [0, 'CHF 0.00'],
    'negative' => [-2500, "CHF -2'500.00"],
]);
