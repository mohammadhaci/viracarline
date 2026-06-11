<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Anfrage')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->disabled(),
                        TextInput::make('email')->disabled(),
                        TextInput::make('phone')->disabled(),
                        TextInput::make('locale')->label('Sprache')->disabled(),
                        Textarea::make('message')->label('Nachricht')->disabled()->rows(4)->columnSpanFull(),
                    ]),
                Section::make('Bearbeitung')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->options([
                                LeadStatus::New->value => 'Neu',
                                LeadStatus::Contacted->value => 'Kontaktiert',
                                LeadStatus::Closed->value => 'Abgeschlossen',
                            ])
                            ->required(),
                        Select::make('assigned_to')
                            ->label('Zugewiesen an')
                            ->relationship('assignee', 'name'),
                    ]),
            ]);
    }
}
