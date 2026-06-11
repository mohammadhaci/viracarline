<?php

namespace App\Filament\Manage\Resources\Vehicles\Tables;

use App\Enums\VehicleStatus;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Services\VehicleTradingService;
use App\Support\SwissMoney;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withSum('costs', 'amount'))
            ->columns([
                TextColumn::make('brand')->label('Marke')->searchable()->sortable(),
                TextColumn::make('model')->label('Modell')->searchable(),
                TextColumn::make('status')->badge()
                    ->color(fn (VehicleStatus $state) => match ($state) {
                        VehicleStatus::Sold => 'success',
                        VehicleStatus::InWorkshop => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('partner.company_name')->label('Partner')->placeholder('—'),
                TextColumn::make('purchase_price')
                    ->label('Einkauf')
                    ->formatStateUsing(fn ($state) => SwissMoney::format($state))
                    ->sortable(),
                TextColumn::make('costs_sum_amount')
                    ->label('Kosten')
                    ->formatStateUsing(fn ($state) => SwissMoney::format($state ?? 0)),
                TextColumn::make('sold_price')
                    ->label('Verkauf')
                    ->formatStateUsing(fn ($state) => $state !== null ? SwissMoney::format($state) : '—'),
                TextColumn::make('margin')
                    ->label('Marge')
                    ->state(fn (Vehicle $record) => $record->sold_price !== null
                        ? SwissMoney::format((float) $record->sold_price - (float) $record->purchase_price - (float) ($record->costs_sum_amount ?? 0))
                        : '—')
                    ->color(fn (Vehicle $record) => $record->sold_price === null ? null : (
                        ((float) $record->sold_price - (float) $record->purchase_price - (float) ($record->costs_sum_amount ?? 0)) >= 0
                            ? 'success'
                            : 'danger'
                    )),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'purchased' => 'Angekauft',
                    'in_workshop' => 'In Werkstatt',
                    'ready' => 'Bereit',
                    'listed' => 'Inseriert',
                    'reserved' => 'Reserviert',
                    'sold' => 'Verkauft',
                ]),
                SelectFilter::make('partner_id')->label('Partner')->relationship('partner', 'company_name'),
            ])
            ->recordActions([
                Action::make('recordSale')
                    ->label('Verkauf erfassen')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Vehicle $record) => $record->status !== VehicleStatus::Sold)
                    ->schema([
                        Select::make('customer_id')
                            ->label('Käufer')
                            ->options(Customer::orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('price')
                            ->label('Verkaufspreis (CHF)')
                            ->numeric()
                            ->required(),
                        Select::make('vat_mode')
                            ->label('MWST-Modus')
                            ->options([
                                'margin' => 'Differenzbesteuerung (Occasion)',
                                'standard' => 'Standard (8.1%)',
                            ])
                            ->default('margin')
                            ->required(),
                        DateTimePicker::make('sold_at')
                            ->label('Verkauft am')
                            ->default(now()),
                    ])
                    ->action(function (Vehicle $record, array $data) {
                        app(VehicleTradingService::class)->recordSale(
                            $record,
                            Customer::findOrFail($data['customer_id']),
                            number_format((float) $data['price'], 2, '.', ''),
                            $data['vat_mode'],
                            $data['sold_at'] ? new \DateTimeImmutable($data['sold_at']) : null,
                        );

                        Notification::make()
                            ->success()
                            ->title('Verkauf erfasst — Vertrag und Rechnungsentwurf erstellt')
                            ->send();
                    }),
                EditAction::make(),
            ]);
    }
}
