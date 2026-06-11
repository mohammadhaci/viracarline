<?php

namespace App\Filament\Workshop\Resources\Inspections\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InspectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.brand')->label('Fahrzeug')
                    ->state(fn ($record) => $record->vehicle ? "{$record->vehicle->brand} {$record->vehicle->model}" : '—'),
                TextColumn::make('result')->label('Ergebnis')->badge()
                    ->color(fn (?string $state) => $state === 'passed' ? 'success' : 'danger')
                    ->formatStateUsing(fn (?string $state) => $state === 'passed' ? 'Bestanden' : 'Mängel'),
                TextColumn::make('inspector.name')->label('Geprüft von'),
                TextColumn::make('created_at')->label('Datum')->dateTime('d.m.Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
