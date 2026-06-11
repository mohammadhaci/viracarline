<?php

namespace App\Filament\Workshop\Resources\RepairOrders\RelationManagers;

use App\Models\Part;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartsRelationManager extends RelationManager
{
    protected static string $relationship = 'parts';

    protected static ?string $title = 'Teile';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('part_id')
                    ->label('Teil')
                    ->options(fn () => Part::orderBy('name')->get()->mapWithKeys(
                        fn (Part $part) => [$part->id => "{$part->name} ({$part->sku}) — Lager: {$part->stock_qty}"],
                    ))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($part = Part::find($state)) {
                            $set('unit_cost', $part->cost_price);
                        }
                    }),
                TextInput::make('qty')
                    ->label('Menge')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
                TextInput::make('unit_cost')
                    ->label('Stückkosten (CHF)')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('part.name')->label('Teil'),
                TextColumn::make('part.sku')->label('SKU'),
                TextColumn::make('qty')->label('Menge'),
                TextColumn::make('unit_cost')->label('Stückkosten'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
