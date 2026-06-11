<?php

namespace App\Filament\Workshop\Resources\Inspections\Schemas;

use App\Models\Inspection;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InspectionForm
{
    public static function configure(Schema $schema): Schema
    {
        $checklist = collect(Inspection::CHECKLIST_ITEMS)
            ->map(
                fn (string $label, string $key) => Radio::make("checklist.{$key}")
                    ->label($label)
                    ->options(['ok' => 'OK', 'issue' => 'Mangel'])
                    ->inline()
                    ->required(),
            )
            ->values()
            ->all();

        return $schema
            ->components([
                Section::make('Ankaufscheck')
                    ->columns(2)
                    ->schema([
                        Select::make('vehicle_id')
                            ->label('Fahrzeug')
                            ->relationship('vehicle', 'slug')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->brand} {$record->model} ({$record->year})")
                            ->searchable()
                            ->required(),
                        Select::make('result')
                            ->label('Ergebnis')
                            ->options(['passed' => 'Bestanden', 'issues' => 'Mängel festgestellt'])
                            ->required(),
                    ]),
                Section::make('Checkliste')
                    ->columns(2)
                    ->schema($checklist),
                Section::make('Details')
                    ->schema([
                        Textarea::make('note')->label('Bemerkungen')->rows(3),
                        SpatieMediaLibraryFileUpload::make('photos')
                            ->collection('photos')
                            ->image()
                            ->multiple()
                            ->label('Fotos'),
                    ]),
            ]);
    }
}
