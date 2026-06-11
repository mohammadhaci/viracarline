<?php

namespace App\Filament\Finance\Resources\Invoices\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Positionen';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('description')
                    ->label('Beschreibung')
                    ->required(),
                TextInput::make('qty')
                    ->label('Menge')
                    ->numeric()
                    ->default(1)
                    ->required(),
                TextInput::make('unit_price')
                    ->label('Einzelpreis (CHF)')
                    ->numeric()
                    ->required(),
                TextInput::make('vat_rate')
                    ->label('MWST-Satz (%)')
                    ->numeric()
                    ->default(8.1)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')->label('Beschreibung'),
                TextColumn::make('qty')->label('Menge'),
                TextColumn::make('unit_price')->label('Einzelpreis'),
                TextColumn::make('vat_rate')->label('MWST %'),
            ])
            ->headerActions([
                CreateAction::make()->after(fn () => $this->getOwnerRecord()->recalculateTotals()),
            ])
            ->recordActions([
                EditAction::make()->after(fn () => $this->getOwnerRecord()->recalculateTotals()),
                DeleteAction::make()->after(fn () => $this->getOwnerRecord()->recalculateTotals()),
            ]);
    }
}
