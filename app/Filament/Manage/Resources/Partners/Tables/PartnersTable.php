<?php

namespace App\Filament\Manage\Resources\Partners\Tables;

use App\Models\Partner;
use App\Services\PartnerAmountService;
use App\Support\SwissMoney;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')->label('Firma / Name')->searchable(),
                TextColumn::make('user.email')->label('Login')->searchable(),
                TextColumn::make('vehicles_count')->label('Fahrzeuge')->counts('vehicles'),
                TextColumn::make('display_amount_override')
                    ->label('Individueller Betrag')
                    ->formatStateUsing(fn ($state) => $state !== null ? SwissMoney::format($state) : '—')
                    ->placeholder('—'),
                TextColumn::make('effective_amount')
                    ->label('Effektiver Betrag')
                    ->state(fn (Partner $record) => SwissMoney::format(app(PartnerAmountService::class)->effectiveAmountFor($record))),
                IconColumn::make('is_active')->label('Aktiv')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
