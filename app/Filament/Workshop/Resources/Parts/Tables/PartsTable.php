<?php

namespace App\Filament\Workshop\Resources\Parts\Tables;

use App\Models\Part;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->label('SKU')->searchable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('stock_qty')
                    ->label('Lager')
                    ->badge()
                    ->color(fn (Part $record) => $record->isLowStock() ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('min_qty')->label('Min.'),
                TextColumn::make('cost_price')->label('EK'),
                TextColumn::make('sale_price')->label('VK'),
            ])
            ->filters([
                Filter::make('low_stock')
                    ->label('Unter Mindestbestand')
                    ->query(fn (Builder $query) => $query->whereColumn('stock_qty', '<=', 'min_qty')),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
