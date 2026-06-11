<?php

namespace App\Filament\Manage\Resources\Partners\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Partner')
                    ->columns(2)
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Firma / Name')
                            ->required(),
                        Select::make('user_id')
                            ->label('Login-Benutzer')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->createOptionForm([
                                TextInput::make('name')->required(),
                                TextInput::make('email')->email()->required()->unique('users', 'email'),
                                TextInput::make('password')->password()->required(),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $user = User::create([
                                    'name' => $data['name'],
                                    'email' => $data['email'],
                                    'password' => Hash::make($data['password'] ?: Str::random(32)),
                                ]);
                                $user->assignRole('partner');

                                return $user->id;
                            }),
                        DatePicker::make('joined_at')->label('Beitritt'),
                        Toggle::make('is_active')->label('Aktiv')->default(true)->inline(false),
                    ]),
                Section::make('Kontakt')
                    ->columns(3)
                    ->schema([
                        TextInput::make('contact.name')->label('Kontaktperson'),
                        TextInput::make('contact.email')->label('E-Mail')->email(),
                        TextInput::make('contact.phone')->label('Telefon'),
                    ]),
                Section::make('Dokumente (Partner-Portal)')
                    ->description('PDFs erscheinen im Partner-Portal unter «Dokumente» und liegen auf dem privaten Datenträger.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('statements')
                            ->collection('statements')
                            ->disk('local')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf'])
                            ->downloadable()
                            ->label('Abrechnungen / Dokumente'),
                    ]),
                Section::make('Anzeigebetrag')
                    ->description('Leer lassen, damit der globale Standardwert gilt (Seite «Partner-Beträge»).')
                    ->columns(2)
                    ->schema([
                        TextInput::make('display_amount_override')
                            ->label('Individueller Betrag (CHF)')
                            ->numeric()
                            ->nullable(),
                        TextInput::make('override_note')
                            ->label('Hinweis')
                            ->placeholder('z. B. «Stand: Juni 2026»'),
                    ]),
            ]);
    }
}
