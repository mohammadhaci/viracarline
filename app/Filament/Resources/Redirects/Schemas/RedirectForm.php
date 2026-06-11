<?php

namespace App\Filament\Resources\Redirects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RedirectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('from_path')
                    ->label('Von (Pfad)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('/alte-seite'),
                TextInput::make('to_path')
                    ->label('Nach (Pfad oder URL)')
                    ->required()
                    ->placeholder('/de/neue-seite'),
                Select::make('status_code')
                    ->options([301 => '301 — dauerhaft', 302 => '302 — temporär'])
                    ->default(301)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktiv')
                    ->default(true),
            ]);
    }
}
