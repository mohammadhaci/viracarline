<?php

namespace App\Filament\Finance\Resources\Invoices\Tables;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Invoice;
use App\Services\InvoicePdfService;
use App\Support\SwissMoney;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->label('Nr.')->searchable()->sortable(),
                TextColumn::make('type')->label('Typ')->badge()
                    ->formatStateUsing(fn (InvoiceType $state) => $state === InvoiceType::VehicleSale ? 'Fahrzeug' : 'Reparatur'),
                TextColumn::make('customer.name')->label('Kunde')->searchable(),
                TextColumn::make('total')->label('Total')
                    ->formatStateUsing(fn ($state) => SwissMoney::format($state))
                    ->sortable(),
                TextColumn::make('status')->badge()
                    ->color(fn (InvoiceStatus $state) => match ($state) {
                        InvoiceStatus::Draft => 'gray',
                        InvoiceStatus::Sent => 'warning',
                        InvoiceStatus::Paid => 'success',
                        InvoiceStatus::Cancelled => 'danger',
                    }),
                TextColumn::make('due_at')->label('Fällig')->date('d.m.Y')->sortable(),
                TextColumn::make('paid_at')->label('Bezahlt')->dateTime('d.m.Y')->placeholder('offen'),
                TextColumn::make('dunning_level')->label('Mahnstufe')
                    ->formatStateUsing(fn (int $state) => $state === 0 ? '—' : (string) $state),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Entwurf',
                    'sent' => 'Versendet',
                    'paid' => 'Bezahlt',
                    'cancelled' => 'Storniert',
                ]),
                Filter::make('open_items')
                    ->label('Offene Posten (Debitoren)')
                    ->query(fn (Builder $query) => $query
                        ->whereNull('paid_at')
                        ->whereNotIn('status', [InvoiceStatus::Cancelled, InvoiceStatus::Draft])),
            ])
            ->recordActions([
                Action::make('markPaid')
                    ->label('Als bezahlt markieren')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Invoice $record) => $record->paid_at === null && $record->status !== InvoiceStatus::Cancelled)
                    ->schema([
                        DateTimePicker::make('paid_at')->label('Bezahlt am')->default(now())->required(),
                        Select::make('payment_method')
                            ->label('Zahlungsart')
                            ->options(['bank' => 'Banküberweisung', 'cash' => 'Bar', 'card' => 'Karte', 'twint' => 'TWINT'])
                            ->default('bank')
                            ->required(),
                    ])
                    ->action(function (Invoice $record, array $data) {
                        $record->update([
                            'status' => InvoiceStatus::Paid,
                            'paid_at' => $data['paid_at'],
                            'payment_method' => $data['payment_method'],
                        ]);

                        Notification::make()->success()->title('Zahlung erfasst')->send();
                    }),
                Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Invoice $record) {
                        $path = app(InvoicePdfService::class)->generate($record);

                        return response()->download(Storage::disk('local')->path($path));
                    }),
                EditAction::make(),
            ]);
    }
}
