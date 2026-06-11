<?php

namespace App\Filament\Workshop\Resources\RepairOrders\Schemas;

use App\Enums\RepairOrderPriority;
use App\Enums\RepairOrderStatus;
use App\Enums\RepairOrderType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class RepairOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Auftrag')
                    ->columns(3)
                    ->schema([
                        Select::make('type')
                            ->label('Typ')
                            ->options([
                                RepairOrderType::Internal->value => 'Intern (Aufbereitung)',
                                RepairOrderType::Customer->value => 'Kundenauftrag',
                            ])
                            ->default(RepairOrderType::Internal->value)
                            ->live()
                            ->required(),
                        Select::make('status')
                            ->options([
                                RepairOrderStatus::Open->value => 'Offen',
                                RepairOrderStatus::InProgress->value => 'In Arbeit',
                                RepairOrderStatus::WaitingParts->value => 'Wartet auf Teile',
                                RepairOrderStatus::Done->value => 'Erledigt',
                                RepairOrderStatus::Invoiced->value => 'Verrechnet',
                            ])
                            ->default(RepairOrderStatus::Open->value)
                            ->required(),
                        Select::make('priority')
                            ->label('Priorität')
                            ->options([
                                RepairOrderPriority::Low->value => 'Tief',
                                RepairOrderPriority::Normal->value => 'Normal',
                                RepairOrderPriority::High->value => 'Hoch',
                            ])
                            ->default(RepairOrderPriority::Normal->value)
                            ->required(),
                        Select::make('assigned_to')
                            ->label('Zugewiesen an')
                            ->relationship('assignee', 'name')
                            ->nullable(),
                    ]),
                Section::make('Fahrzeug')
                    ->columns(2)
                    ->schema([
                        Select::make('vehicle_id')
                            ->label('Fahrzeug aus Bestand')
                            ->relationship('vehicle', 'slug')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->brand} {$record->model} ({$record->year})")
                            ->searchable()
                            ->visible(fn (Get $get) => $get('type') === RepairOrderType::Internal->value)
                            ->requiredIf('type', RepairOrderType::Internal->value),
                        Select::make('customer_id')
                            ->label('Kunde')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get) => $get('type') === RepairOrderType::Customer->value)
                            ->requiredIf('type', RepairOrderType::Customer->value),
                        TextInput::make('customer_vehicle_info.brand')
                            ->label('Kundenfahrzeug: Marke/Modell')
                            ->visible(fn (Get $get) => $get('type') === RepairOrderType::Customer->value),
                        TextInput::make('customer_vehicle_info.plate')
                            ->label('Kontrollschild')
                            ->visible(fn (Get $get) => $get('type') === RepairOrderType::Customer->value),
                    ]),
                Section::make('Diagnose')
                    ->schema([
                        Textarea::make('diagnosis')
                            ->label('Gemeldete Probleme / Diagnose')
                            ->rows(4),
                    ]),
            ]);
    }
}
