<?php

namespace App\Filament\Workshop\Resources\Inspections;

use App\Filament\Workshop\Resources\Inspections\Pages\CreateInspection;
use App\Filament\Workshop\Resources\Inspections\Pages\EditInspection;
use App\Filament\Workshop\Resources\Inspections\Pages\ListInspections;
use App\Filament\Workshop\Resources\Inspections\Schemas\InspectionForm;
use App\Filament\Workshop\Resources\Inspections\Tables\InspectionsTable;
use App\Models\Inspection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InspectionResource extends Resource
{
    protected static ?string $model = Inspection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return InspectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InspectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInspections::route('/'),
            'create' => CreateInspection::route('/create'),
            'edit' => EditInspection::route('/{record}/edit'),
        ];
    }
}
