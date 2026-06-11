<?php

namespace App\Filament\Workshop\Widgets;

use App\Enums\RepairOrderPriority;
use App\Enums\RepairOrderStatus;
use App\Models\RepairOrder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * "My Day" (plan §3.3): open orders assigned to me, highest priority first.
 */
class MyDay extends TableWidget
{
    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Mein Tag')
            ->query(
                RepairOrder::query()
                    ->where('assigned_to', auth()->id())
                    ->whereNotIn('status', [RepairOrderStatus::Done, RepairOrderStatus::Invoiced])
                    ->orderByRaw("CASE priority WHEN 'high' THEN 0 WHEN 'normal' THEN 1 ELSE 2 END"),
            )
            ->columns([
                TextColumn::make('number')->label('Nr.'),
                TextColumn::make('vehicle.brand')->label('Fahrzeug')
                    ->state(fn ($record) => $record->vehicle
                        ? "{$record->vehicle->brand} {$record->vehicle->model}"
                        : ($record->customer_vehicle_info['brand'] ?? '—')),
                TextColumn::make('priority')->label('Priorität')->badge()
                    ->color(fn (RepairOrderPriority $state) => match ($state) {
                        RepairOrderPriority::High => 'danger',
                        RepairOrderPriority::Normal => 'info',
                        RepairOrderPriority::Low => 'gray',
                    }),
                TextColumn::make('status')->badge(),
                TextColumn::make('diagnosis')->label('Diagnose')->limit(60),
            ])
            ->paginated(false);
    }
}
