<?php

namespace App\Filament\Resources\PostCategories\Schemas;

use App\Filament\Support\TranslatableTabs;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PostCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                TranslatableTabs::make(fn (string $locale): array => [
                    TextInput::make("name.{$locale}")
                        ->label('Name')
                        ->required($locale === config('locales.default')),
                ]),
            ]);
    }
}
