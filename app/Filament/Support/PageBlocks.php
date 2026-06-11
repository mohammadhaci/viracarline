<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

/**
 * The initial ~20-block library for the page builder (plan.md §3.1).
 * Blocks store plain data; rendering happens on the public site (Phase 3).
 */
class PageBlocks
{
    /**
     * @return array<int, Block>
     */
    public static function all(): array
    {
        return [
            Block::make('hero')->label('Hero')->schema([
                TextInput::make('heading')->required(),
                TextInput::make('subheading'),
                self::image('background'),
                TextInput::make('cta_label')->label('Button-Text'),
                TextInput::make('cta_url')->label('Button-Link'),
            ]),

            Block::make('car_grid')->label('Fahrzeug-Raster')->schema([
                TextInput::make('heading'),
                TextInput::make('limit')->numeric()->default(6),
                Toggle::make('featured_only')->label('Nur hervorgehobene Fahrzeuge'),
            ]),

            Block::make('car_slider')->label('Fahrzeug-Slider')->schema([
                TextInput::make('heading'),
                TextInput::make('limit')->numeric()->default(8),
            ]),

            Block::make('feature_list')->label('Feature-Liste')->schema([
                TextInput::make('heading'),
                Repeater::make('features')->schema([
                    TextInput::make('icon')->placeholder('z. B. heroicon-o-wrench'),
                    TextInput::make('title')->required(),
                    Textarea::make('text')->rows(2),
                ])->defaultItems(3),
            ]),

            Block::make('stats_counters')->label('Statistik-Zähler')->schema([
                Repeater::make('stats')->schema([
                    TextInput::make('value')->required()->placeholder('z. B. 250+'),
                    TextInput::make('label')->required(),
                ])->defaultItems(3),
            ]),

            Block::make('image_text')->label('Bild + Text')->schema([
                self::image('image'),
                TextInput::make('heading'),
                RichEditor::make('text'),
                Select::make('image_position')->options(['left' => 'Links', 'right' => 'Rechts'])->default('left'),
            ]),

            Block::make('gallery')->label('Galerie')->schema([
                FileUpload::make('images')->image()->multiple()->disk('public')->directory('blocks'),
            ]),

            Block::make('testimonials')->label('Kundenstimmen')->schema([
                Repeater::make('testimonials')->schema([
                    TextInput::make('name')->required(),
                    Textarea::make('quote')->required()->rows(3),
                ]),
            ]),

            Block::make('team')->label('Team')->schema([
                TextInput::make('heading'),
                Repeater::make('members')->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('role'),
                    self::image('photo'),
                ]),
            ]),

            Block::make('faq')->label('FAQ Akkordeon')->schema([
                TextInput::make('heading'),
                Repeater::make('items')->schema([
                    TextInput::make('question')->required(),
                    Textarea::make('answer')->required()->rows(3),
                ]),
            ]),

            Block::make('cta_banner')->label('CTA-Banner')->schema([
                TextInput::make('heading')->required(),
                TextInput::make('text'),
                TextInput::make('cta_label'),
                TextInput::make('cta_url'),
            ]),

            Block::make('contact_form')->label('Kontaktformular')->schema([
                TextInput::make('heading'),
                TextInput::make('intro'),
            ]),

            Block::make('map')->label('Karte')->schema([
                TextInput::make('embed_url')->label('Google Maps Embed-URL')->url(),
            ]),

            Block::make('services_grid')->label('Services-Raster')->schema([
                TextInput::make('heading'),
                Repeater::make('services')->schema([
                    TextInput::make('icon'),
                    TextInput::make('title')->required(),
                    Textarea::make('text')->rows(2),
                ]),
            ]),

            Block::make('finance_teaser')->label('Finanzierungs-Teaser')->schema([
                TextInput::make('heading'),
                Textarea::make('text')->rows(3),
                TextInput::make('cta_label'),
                TextInput::make('cta_url'),
            ]),

            Block::make('partner_logos')->label('Partner-Logos')->schema([
                FileUpload::make('logos')->image()->multiple()->disk('public')->directory('blocks'),
            ]),

            Block::make('video_embed')->label('Video')->schema([
                TextInput::make('url')->label('YouTube/Vimeo-URL')->url()->required(),
            ]),

            Block::make('rich_text')->label('Fliesstext')->schema([
                RichEditor::make('content'),
            ]),

            Block::make('spacer')->label('Abstand')->schema([
                Select::make('size')->options(['sm' => 'Klein', 'md' => 'Mittel', 'lg' => 'Gross'])->default('md'),
            ]),

            Block::make('custom_html')->label('Eigenes HTML')->schema([
                Textarea::make('html')->rows(6)->helperText('Wird ungefiltert ausgegeben — nur für Admins.'),
            ]),
        ];
    }

    private static function image(string $name): FileUpload
    {
        return FileUpload::make($name)->image()->disk('public')->directory('blocks');
    }
}
