<?php

namespace App\Filament\Finance\Resources\Expenses\Schemas;

use App\Models\Expense;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category')
                    ->label('Kategorie')
                    ->options(Expense::CATEGORIES)
                    ->required(),
                TextInput::make('amount')
                    ->label('Betrag (CHF)')
                    ->numeric()
                    ->required(),
                TextInput::make('vat_amount')
                    ->label('Davon MWST (CHF)')
                    ->numeric()
                    ->default(0),
                DatePicker::make('date')
                    ->label('Datum')
                    ->default(now())
                    ->required(),
                TextInput::make('vendor')
                    ->label('Lieferant'),
                Select::make('vehicle_id')
                    ->label('Fahrzeug (optional)')
                    ->relationship('vehicle', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->brand} {$record->model} ({$record->year})")
                    ->searchable()
                    ->nullable(),
                TextInput::make('note')->label('Bemerkung'),
                SpatieMediaLibraryFileUpload::make('receipt')
                    ->collection('receipt')
                    ->disk('local')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->label('Beleg'),
            ]);
    }
}
