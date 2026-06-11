<?php

namespace App\Filament\Workshop\Resources\Parts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->required(),
                TextInput::make('stock_qty')
                    ->label('Lagerbestand')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('min_qty')
                    ->label('Mindestbestand')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('cost_price')
                    ->label('Einkaufspreis (CHF)')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('sale_price')
                    ->label('Verkaufspreis (CHF)')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
