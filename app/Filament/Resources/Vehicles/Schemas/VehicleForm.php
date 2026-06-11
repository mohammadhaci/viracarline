<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Enums\VehicleStatus;
use App\Filament\Support\TranslatableTabs;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Fahrzeug')
                    ->columns(3)
                    ->schema([
                        TextInput::make('brand')->label('Marke')->required(),
                        TextInput::make('model')->label('Modell')->required(),
                        TextInput::make('variant')->label('Variante'),
                        TextInput::make('vin')->label('VIN')->maxLength(17)->unique(ignoreRecord: true),
                        TextInput::make('year')->label('Jahrgang')->numeric()->required(),
                        TextInput::make('mileage_km')->label('Kilometerstand')->numeric()->required()->suffix('km'),
                        Select::make('fuel')->label('Treibstoff')->options([
                            'petrol' => 'Benzin',
                            'diesel' => 'Diesel',
                            'hybrid' => 'Hybrid',
                            'electric' => 'Elektro',
                        ])->required(),
                        Select::make('transmission')->label('Getriebe')->options([
                            'manual' => 'Manuell',
                            'automatic' => 'Automat',
                        ])->required(),
                        TextInput::make('color')->label('Farbe'),
                        Select::make('status')
                            ->options([
                                VehicleStatus::Purchased->value => 'Angekauft',
                                VehicleStatus::InWorkshop->value => 'In Werkstatt',
                                VehicleStatus::Ready->value => 'Bereit',
                                VehicleStatus::Listed->value => 'Inseriert',
                                VehicleStatus::Reserved->value => 'Reserviert',
                                VehicleStatus::Sold->value => 'Verkauft',
                                VehicleStatus::Archived->value => 'Archiviert',
                            ])
                            ->required(),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true),
                    ]),
                Section::make('Inserat (öffentliche Website)')
                    ->columns(3)
                    ->schema([
                        TextInput::make('asking_price')->label('Verkaufspreis')->numeric()->prefix('CHF'),
                        Toggle::make('show_price')->label('Preis anzeigen')->inline(false)
                            ->helperText('Aus = «Preis auf Anfrage»'),
                        Toggle::make('is_published')->label('Veröffentlicht')->inline(false),
                        Toggle::make('is_featured')->label('Hervorgehoben')->inline(false),
                        SpatieMediaLibraryFileUpload::make('photos')
                            ->collection('photos')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->label('Fotos')
                            ->columnSpanFull(),
                    ]),
                TranslatableTabs::make(fn (string $locale): array => [
                    TextInput::make("title.{$locale}")
                        ->label('Inserat-Titel')
                        ->required($locale === config('locales.default')),
                    RichEditor::make("description.{$locale}")
                        ->label('Beschreibung'),
                ]),
            ]);
    }
}
