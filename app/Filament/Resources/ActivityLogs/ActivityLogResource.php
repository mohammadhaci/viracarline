<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'Aktivität';

    protected static ?string $pluralModelLabel = 'Aktivitätsprotokoll';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Zeitpunkt')->dateTime('d.m.Y H:i:s')->sortable(),
                TextColumn::make('causer.name')->label('Benutzer')->placeholder('System'),
                TextColumn::make('event')->label('Ereignis')->badge(),
                TextColumn::make('subject_type')
                    ->label('Objekt')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—'),
                TextColumn::make('subject_id')->label('ID'),
                TextColumn::make('properties')
                    ->label('Änderungen')
                    ->formatStateUsing(fn ($record) => json_encode($record->properties, JSON_UNESCAPED_UNICODE))
                    ->limit(80)
                    ->tooltip(fn ($record) => json_encode($record->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event')->options([
                    'created' => 'created',
                    'updated' => 'updated',
                    'deleted' => 'deleted',
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
        ];
    }
}
