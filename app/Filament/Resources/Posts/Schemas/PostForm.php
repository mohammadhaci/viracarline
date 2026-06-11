<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Support\TranslatableTabs;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Beitrag')
                    ->columns(3)
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Select::make('post_category_id')
                            ->label('Kategorie')
                            ->relationship('category', 'slug')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                        Toggle::make('is_published')
                            ->label('Veröffentlicht')
                            ->inline(false),
                        DateTimePicker::make('published_at')
                            ->label('Veröffentlicht am'),
                        SpatieMediaLibraryFileUpload::make('featured_image')
                            ->collection('featured_image')
                            ->image()
                            ->label('Beitragsbild'),
                    ]),
                TranslatableTabs::make(fn (string $locale): array => [
                    TextInput::make("title.{$locale}")
                        ->label('Titel')
                        ->required($locale === config('locales.default')),
                    Textarea::make("excerpt.{$locale}")
                        ->label('Anriss')
                        ->rows(2),
                    RichEditor::make("body.{$locale}")
                        ->label('Inhalt'),
                ]),
            ]);
    }
}
