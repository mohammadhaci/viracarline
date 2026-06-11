<?php

namespace App\Filament\Partner\Widgets;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use App\Support\SwissMoney;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class MyVehicles extends TableWidget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Meine Fahrzeuge')
            // Strict scoping (plan §3.4): only vehicles linked to MY partner record.
            ->query(
                Vehicle::query()->where(
                    'partner_id',
                    auth()->user()->partner?->id ?? -1,
                ),
            )
            ->columns([
                TextColumn::make('brand')->label('Marke'),
                TextColumn::make('model')->label('Modell'),
                TextColumn::make('year')->label('Jahrgang'),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn (VehicleStatus $state) => match ($state) {
                        VehicleStatus::Purchased => 'An Lager',
                        VehicleStatus::InWorkshop => 'In Werkstatt',
                        VehicleStatus::Ready => 'Bereit',
                        VehicleStatus::Listed => 'Inseriert',
                        VehicleStatus::Reserved => 'Reserviert',
                        VehicleStatus::Sold => 'Verkauft',
                        VehicleStatus::Archived => 'Archiviert',
                    })
                    ->color(fn (VehicleStatus $state) => match ($state) {
                        VehicleStatus::Sold => 'success',
                        VehicleStatus::InWorkshop => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('sold_price')
                    ->label('Verkaufsresultat')
                    ->formatStateUsing(fn ($state) => $state !== null ? SwissMoney::format($state) : null)
                    ->placeholder('—'),
            ])
            ->paginated(false);
    }
}
