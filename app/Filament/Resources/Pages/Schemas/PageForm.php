<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Filament\Support\PageBlocks;
use App\Filament\Support\TranslatableTabs;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Seite')
                    ->columns(3)
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('URL-Pfad, z. B. "ueber-uns"'),
                        Select::make('template')
                            ->options([
                                'default' => 'Standard',
                                'home' => 'Startseite',
                                'contact' => 'Kontakt',
                            ])
                            ->default('default')
                            ->required(),
                        Toggle::make('is_published')
                            ->label('Veröffentlicht')
                            ->inline(false),
                        DateTimePicker::make('published_at')
                            ->label('Veröffentlicht am'),
                        SpatieMediaLibraryFileUpload::make('og_image')
                            ->collection('og_image')
                            ->image()
                            ->label('OG-Bild (Social Sharing)'),
                    ]),
                TranslatableTabs::make(fn (string $locale): array => [
                    TextInput::make("title.{$locale}")
                        ->label('Titel')
                        ->required($locale === config('locales.default')),
                    Builder::make("blocks.{$locale}")
                        ->label('Inhalt')
                        ->blocks(PageBlocks::all())
                        ->collapsible(),
                    TextInput::make("seo_title.{$locale}")
                        ->label('SEO-Titel')
                        ->maxLength(70),
                    Textarea::make("seo_description.{$locale}")
                        ->label('SEO-Beschreibung')
                        ->rows(2)
                        ->maxLength(170),
                ]),
            ]);
    }
}
