<?php

namespace App\Filament\Finance\Resources\Expenses\Tables;

use App\Models\Expense;
use App\Support\SwissMoney;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->label('Datum')->date('d.m.Y')->sortable(),
                TextColumn::make('category')->label('Kategorie')->badge()
                    ->formatStateUsing(fn (string $state) => Expense::CATEGORIES[$state] ?? $state),
                TextColumn::make('vendor')->label('Lieferant')->searchable(),
                TextColumn::make('amount')->label('Betrag')
                    ->formatStateUsing(fn ($state) => SwissMoney::format($state))
                    ->sortable(),
                TextColumn::make('vat_amount')->label('MWST')
                    ->formatStateUsing(fn ($state) => SwissMoney::format($state)),
                TextColumn::make('vehicle.brand')->label('Fahrzeug')
                    ->state(fn ($record) => $record->vehicle ? "{$record->vehicle->brand} {$record->vehicle->model}" : '—'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('category')->options(Expense::CATEGORIES),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
