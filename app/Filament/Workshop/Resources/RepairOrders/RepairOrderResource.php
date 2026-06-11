<?php

namespace App\Filament\Workshop\Resources\RepairOrders;

use App\Filament\Workshop\Resources\RepairOrders\Pages\CreateRepairOrder;
use App\Filament\Workshop\Resources\RepairOrders\Pages\EditRepairOrder;
use App\Filament\Workshop\Resources\RepairOrders\Pages\ListRepairOrders;
use App\Filament\Workshop\Resources\RepairOrders\RelationManagers\PartsRelationManager;
use App\Filament\Workshop\Resources\RepairOrders\RelationManagers\TasksRelationManager;
use App\Filament\Workshop\Resources\RepairOrders\Schemas\RepairOrderForm;
use App\Filament\Workshop\Resources\RepairOrders\Tables\RepairOrdersTable;
use App\Models\RepairOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RepairOrderResource extends Resource
{
    protected static ?string $model = RepairOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return RepairOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RepairOrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class,
            PartsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRepairOrders::route('/'),
            'create' => CreateRepairOrder::route('/create'),
            'edit' => EditRepairOrder::route('/{record}/edit'),
        ];
    }
}
