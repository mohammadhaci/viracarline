<?php

namespace App\Filament\Workshop\Resources\RepairOrders\Tables;

use App\Enums\RepairOrderPriority;
use App\Enums\RepairOrderStatus;
use App\Enums\RepairOrderType;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RepairOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->label('Nr.')->searchable()->sortable(),
                TextColumn::make('type')->label('Typ')->badge()
                    ->formatStateUsing(fn (RepairOrderType $state) => $state === RepairOrderType::Internal ? 'Intern' : 'Kunde'),
                TextColumn::make('vehicle.brand')->label('Fahrzeug')
                    ->state(fn ($record) => $record->vehicle
                        ? "{$record->vehicle->brand} {$record->vehicle->model}"
                        : ($record->customer_vehicle_info['brand'] ?? '—')),
                TextColumn::make('customer.name')->label('Kunde')->placeholder('—'),
                TextColumn::make('status')->badge()
                    ->color(fn (RepairOrderStatus $state) => match ($state) {
                        RepairOrderStatus::Open => 'danger',
                        RepairOrderStatus::InProgress => 'warning',
                        RepairOrderStatus::WaitingParts => 'gray',
                        RepairOrderStatus::Done, RepairOrderStatus::Invoiced => 'success',
                    }),
                TextColumn::make('priority')->label('Priorität')->badge()
                    ->color(fn (RepairOrderPriority $state) => match ($state) {
                        RepairOrderPriority::High => 'danger',
                        RepairOrderPriority::Normal => 'info',
                        RepairOrderPriority::Low => 'gray',
                    }),
                TextColumn::make('assignee.name')->label('Zugewiesen'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'open' => 'Offen',
                    'in_progress' => 'In Arbeit',
                    'waiting_parts' => 'Wartet auf Teile',
                    'done' => 'Erledigt',
                    'invoiced' => 'Verrechnet',
                ]),
                SelectFilter::make('type')->options([
                    'internal' => 'Intern',
                    'customer' => 'Kunde',
                ]),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
