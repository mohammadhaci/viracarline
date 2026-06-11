<?php

namespace App\Filament\Finance\Resources\PartnerPayouts\Tables;

use App\Support\SwissMoney;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartnerPayoutsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->label('Datum')->date('d.m.Y')->sortable(),
                TextColumn::make('partner.company_name')->label('Partner')->searchable(),
                TextColumn::make('amount')->label('Betrag')
                    ->formatStateUsing(fn ($state) => SwissMoney::format($state))
                    ->sortable(),
                TextColumn::make('reference')->label('Referenz'),
                TextColumn::make('creator.name')->label('Erfasst von'),
            ])
            ->defaultSort('date', 'desc')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
