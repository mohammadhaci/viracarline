<?php

namespace App\Filament\Finance\Resources\PartnerPayouts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PartnerPayoutForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('partner_id')
                    ->label('Partner')
                    ->relationship('partner', 'company_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('amount')
                    ->label('Betrag (CHF)')
                    ->numeric()
                    ->required(),
                DatePicker::make('date')
                    ->label('Datum')
                    ->default(now())
                    ->required(),
                TextInput::make('reference')
                    ->label('Referenz')
                    ->placeholder('z. B. Zahlungsreferenz'),
                TextInput::make('note')->label('Bemerkung'),
            ]);
    }
}
