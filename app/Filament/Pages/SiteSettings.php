<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class SiteSettings extends Page
{
    protected string $view = 'filament.pages.site-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $title = 'Website-Einstellungen';

    /** @var list<string> */
    private const KEYS = [
        'site_company_name',
        'site_address',
        'site_uid',
        'site_phone',
        'site_email',
        'site_opening_hours',
        'site_social_instagram',
        'site_social_facebook',
        'site_whatsapp',
        'site_analytics_script',
        'site_maintenance_mode',
    ];

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            collect(self::KEYS)->mapWithKeys(fn (string $key) => [$key => Setting::get($key)])->all(),
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Firma')
                    ->columns(2)
                    ->schema([
                        TextInput::make('site_company_name')->label('Firmenname')->placeholder('Vira Car Lines AG'),
                        TextInput::make('site_uid')->label('UID / CHE-Nummer'),
                        Textarea::make('site_address')->label('Adresse')->rows(3),
                        Textarea::make('site_opening_hours')->label('Öffnungszeiten')->rows(3),
                        TextInput::make('site_phone')->label('Telefon'),
                        TextInput::make('site_email')->label('E-Mail')->email(),
                    ]),
                Section::make('Social & Kontakt')
                    ->columns(2)
                    ->schema([
                        TextInput::make('site_social_instagram')->label('Instagram')->url(),
                        TextInput::make('site_social_facebook')->label('Facebook')->url(),
                        TextInput::make('site_whatsapp')->label('WhatsApp-Nummer'),
                    ]),
                Section::make('System')
                    ->columns(1)
                    ->schema([
                        Textarea::make('site_analytics_script')
                            ->label('Analytics-Script')
                            ->rows(3)
                            ->helperText('Wird im <head> der öffentlichen Website ausgegeben.'),
                        Toggle::make('site_maintenance_mode')->label('Wartungsmodus (öffentliche Website)'),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Speichern')
                                ->submit('save'),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        foreach ($this->form->getState() as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->success()
            ->title('Einstellungen gespeichert')
            ->send();
    }
}
