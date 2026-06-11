<?php

namespace App\Filament\Workshop\Resources\RepairOrders\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $title = 'Aufgaben';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('description')
                    ->label('Beschreibung')
                    ->required(),
                TextInput::make('labor_hours')
                    ->label('Arbeitsstunden')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_done')->label('Erledigt'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')->label('Beschreibung'),
                TextColumn::make('labor_hours')->label('Stunden'),
                ToggleColumn::make('is_done')->label('Erledigt'),
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
