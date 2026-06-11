<?php

namespace App\Filament\Support;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * Side-by-side DE/FR/EN editing: one tab per supported locale, fields are
 * bound to "{attribute}.{locale}" paths which spatie/laravel-translatable
 * persists as per-locale JSON.
 */
class TranslatableTabs
{
    /**
     * @param  Closure(string $locale): array<int, Component|Field>  $fields
     */
    public static function make(Closure $fields): Tabs
    {
        $tabs = [];

        foreach (config('locales.supported') as $locale) {
            $tabs[] = Tab::make(strtoupper($locale))->schema($fields($locale));
        }

        return Tabs::make('translations')
            ->label('Inhalt (DE / FR / EN)')
            ->tabs($tabs)
            ->columnSpanFull();
    }
}
