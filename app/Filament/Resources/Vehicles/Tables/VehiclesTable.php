<?php

namespace App\Filament\Resources\Vehicles\Tables;

use App\Enums\VehicleStatus;
use App\Support\SwissMoney;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('brand')->label('Marke')->searchable()->sortable(),
                TextColumn::make('model')->label('Modell')->searchable(),
                TextColumn::make('year')->label('Jahrgang')->sortable(),
                TextColumn::make('mileage_km')->label('km')->numeric()->sortable(),
                TextColumn::make('asking_price')
                    ->label('Preis')
                    ->formatStateUsing(fn ($state) => $state !== null ? SwissMoney::format($state) : '—')
                    ->sortable(),
                TextColumn::make('status')->badge()
                    ->color(fn (VehicleStatus $state) => match ($state) {
                        VehicleStatus::Listed => 'success',
                        VehicleStatus::Sold => 'gray',
                        VehicleStatus::InWorkshop => 'warning',
                        default => 'info',
                    }),
                ToggleColumn::make('is_published')->label('Veröffentlicht'),
                ToggleColumn::make('is_featured')->label('Hervorgehoben'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'purchased' => 'Angekauft',
                    'in_workshop' => 'In Werkstatt',
                    'ready' => 'Bereit',
                    'listed' => 'Inseriert',
                    'reserved' => 'Reserviert',
                    'sold' => 'Verkauft',
                    'archived' => 'Archiviert',
                ]),
                TernaryFilter::make('is_published')->label('Veröffentlicht'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
