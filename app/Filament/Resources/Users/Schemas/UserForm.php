<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create')
                    ->helperText('Beim Bearbeiten leer lassen, um das Passwort zu behalten.'),
                Select::make('roles')
                    ->label('Rollen')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
                Select::make('locale')
                    ->options(['de' => 'Deutsch', 'fr' => 'Français', 'en' => 'English'])
                    ->default('de'),
                Toggle::make('is_active')
                    ->label('Aktiv')
                    ->default(true)
                    ->helperText('Deaktivierte Benutzer können sich in keinem Panel anmelden.'),
            ]);
    }
}
