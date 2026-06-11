<?php

namespace App\Filament\Finance\Resources\Invoices\Schemas;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rechnung')
                    ->columns(3)
                    ->schema([
                        TextInput::make('number')
                            ->label('Nummer')
                            ->disabled()
                            ->dehydrated(false),
                        Select::make('type')
                            ->label('Typ')
                            ->options([
                                InvoiceType::VehicleSale->value => 'Fahrzeugverkauf',
                                InvoiceType::Repair->value => 'Reparatur',
                            ])
                            ->required(),
                        Select::make('status')
                            ->options([
                                InvoiceStatus::Draft->value => 'Entwurf',
                                InvoiceStatus::Sent->value => 'Versendet',
                                InvoiceStatus::Paid->value => 'Bezahlt',
                                InvoiceStatus::Cancelled->value => 'Storniert',
                            ])
                            ->required(),
                        Select::make('customer_id')
                            ->label('Kunde')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload(),
                        DatePicker::make('due_at')->label('Fällig am'),
                        Select::make('dunning_level')
                            ->label('Mahnstufe')
                            ->options([0 => '—', 1 => '1. Mahnung', 2 => '2. Mahnung', 3 => 'Betreibung'])
                            ->default(0),
                    ]),
                Section::make('Zahlung')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('paid_at')->label('Bezahlt am'),
                        Select::make('payment_method')
                            ->label('Zahlungsart')
                            ->options([
                                'bank' => 'Banküberweisung',
                                'cash' => 'Bar',
                                'card' => 'Karte',
                                'twint' => 'TWINT',
                            ]),
                    ]),
            ]);
    }
}
