<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Eingang')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('type')->label('Typ')->badge()
                    ->formatStateUsing(fn (LeadType $state) => match ($state) {
                        LeadType::Contact => 'Kontakt',
                        LeadType::VehicleInquiry => 'Fahrzeuganfrage',
                    }),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('vehicle.title')->label('Fahrzeug')->toggleable(),
                TextColumn::make('status')->badge()
                    ->color(fn (LeadStatus $state) => match ($state) {
                        LeadStatus::New => 'danger',
                        LeadStatus::Contacted => 'warning',
                        LeadStatus::Closed => 'success',
                    }),
                TextColumn::make('assignee.name')->label('Zugewiesen an'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'new' => 'Neu',
                    'contacted' => 'Kontaktiert',
                    'closed' => 'Abgeschlossen',
                ]),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
