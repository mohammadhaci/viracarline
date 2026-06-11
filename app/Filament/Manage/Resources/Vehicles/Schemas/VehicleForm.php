<?php

namespace App\Filament\Manage\Resources\Vehicles\Schemas;

use App\Enums\VehicleStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Fahrzeug')
                    ->columns(3)
                    ->schema([
                        TextInput::make('brand')->label('Marke')->required(),
                        TextInput::make('model')->label('Modell')->required(),
                        TextInput::make('year')->label('Jahrgang')->numeric()->required(),
                        Select::make('status')
                            ->options([
                                VehicleStatus::Purchased->value => 'Angekauft',
                                VehicleStatus::InWorkshop->value => 'In Werkstatt',
                                VehicleStatus::Ready->value => 'Bereit',
                                VehicleStatus::Listed->value => 'Inseriert',
                                VehicleStatus::Reserved->value => 'Reserviert',
                                VehicleStatus::Sold->value => 'Verkauft',
                                VehicleStatus::Archived->value => 'Archiviert',
                            ])
                            ->required(),
                        Select::make('partner_id')
                            ->label('Partner')
                            ->relationship('partner', 'company_name')
                            ->nullable(),
                    ]),
                Section::make('Ankauf (Einkauf)')
                    ->columns(3)
                    ->schema([
                        TextInput::make('purchase_price')->label('Einkaufspreis (CHF)')->numeric()->required(),
                        DatePicker::make('purchase_date')->label('Kaufdatum')->required(),
                        TextInput::make('purchase_source')->label('Quelle'),
                    ]),
                Section::make('Verkauf')
                    ->columns(3)
                    ->schema([
                        TextInput::make('asking_price')->label('Verkaufspreis (Inserat, CHF)')->numeric()->nullable(),
                        TextInput::make('sold_price')->label('Verkaufspreis (effektiv, CHF)')->numeric()->nullable(),
                        DateTimePicker::make('sold_at')->label('Verkauft am')->nullable(),
                    ]),
            ]);
    }
}
