<?php

namespace App\Filament\Resources\Menus\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Menüeinträge';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label.de')->label('Label (DE)')->required(),
                TextInput::make('label.fr')->label('Label (FR)'),
                TextInput::make('label.en')->label('Label (EN)'),
                Select::make('page_id')
                    ->label('Seite')
                    ->relationship('page', 'slug')
                    ->helperText('Entweder eine Seite wählen …'),
                TextInput::make('url')
                    ->label('… oder eine URL angeben')
                    ->placeholder('/de/fahrzeuge oder https://…'),
                Select::make('parent_id')
                    ->label('Übergeordneter Eintrag')
                    ->options(fn () => $this->getOwnerRecord()->items()->pluck('label', 'id')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->label('Label'),
                TextColumn::make('page.slug')->label('Seite'),
                TextColumn::make('url')->label('URL'),
                TextColumn::make('parent.label')->label('Übergeordnet'),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->headerActions([
                CreateAction::make()->mutateDataUsing(function (array $data): array {
                    $data['sort_order'] ??= ((int) $this->getOwnerRecord()->items()->max('sort_order')) + 1;

                    return $data;
                }),
            ])
            ->recordActions([
                EditAction::make()->mutateRecordDataUsing(function (array $data, $record): array {
                    $data['label'] = $record->getTranslations('label');

                    return $data;
                }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
