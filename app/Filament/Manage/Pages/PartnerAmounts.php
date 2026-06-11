<?php

namespace App\Filament\Manage\Pages;

use App\Models\Partner;
use App\Models\Setting;
use App\Services\PartnerAmountService;
use App\Support\SwissMoney;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

/**
 * The GM's control page for the partner dashboard amount (plan §3.2):
 * global default on top, per-partner overrides inline in the table below.
 * Every change is recorded in the activity log.
 *
 * @property-read Schema $form
 */
class PartnerAmounts extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.manage.pages.partner-amounts';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $title = 'Partner-Beträge';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'default_amount' => Setting::get(PartnerAmountService::DEFAULT_AMOUNT_KEY, '0.00'),
            'note' => Setting::get(PartnerAmountService::NOTE_KEY),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Globaler Standardwert')
                    ->description('Gilt für alle Partner ohne individuellen Betrag. Jede Änderung wird protokolliert.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('default_amount')
                            ->label('Betrag (CHF)')
                            ->numeric()
                            ->required(),
                        TextInput::make('note')
                            ->label('Hinweis (für alle Partner sichtbar)')
                            ->placeholder('z. B. «Stand: Juni 2026»'),
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
                            Action::make('save')->label('Speichern')->submit('save'),
                        ]),
                    ]),
                EmbeddedTable::make(),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Setting::set(PartnerAmountService::DEFAULT_AMOUNT_KEY, number_format((float) $state['default_amount'], 2, '.', ''));
        Setting::set(PartnerAmountService::NOTE_KEY, $state['note'] ?? null);

        Notification::make()
            ->success()
            ->title('Standardwert gespeichert')
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Partner::query()->with('user'))
            ->columns([
                TextColumn::make('company_name')->label('Partner')->searchable(),
                TextInputColumn::make('display_amount_override')
                    ->label('Individueller Betrag (CHF)')
                    ->rules(['nullable', 'numeric', 'min:0'])
                    ->placeholder('Standard'),
                TextInputColumn::make('override_note')
                    ->label('Individueller Hinweis')
                    ->placeholder('—'),
                TextColumn::make('effective_amount')
                    ->label('Effektiver Betrag')
                    ->weight('bold')
                    ->state(fn (Partner $record) => SwissMoney::format(app(PartnerAmountService::class)->effectiveAmountFor($record))),
            ])
            ->paginated(false);
    }
}
