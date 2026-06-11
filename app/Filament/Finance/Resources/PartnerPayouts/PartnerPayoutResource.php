<?php

namespace App\Filament\Finance\Resources\PartnerPayouts;

use App\Filament\Finance\Resources\PartnerPayouts\Pages\CreatePartnerPayout;
use App\Filament\Finance\Resources\PartnerPayouts\Pages\EditPartnerPayout;
use App\Filament\Finance\Resources\PartnerPayouts\Pages\ListPartnerPayouts;
use App\Filament\Finance\Resources\PartnerPayouts\Schemas\PartnerPayoutForm;
use App\Filament\Finance\Resources\PartnerPayouts\Tables\PartnerPayoutsTable;
use App\Models\PartnerPayout;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PartnerPayoutResource extends Resource
{
    protected static ?string $model = PartnerPayout::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PartnerPayoutForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartnerPayoutsTable::configure($table);
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
            'index' => ListPartnerPayouts::route('/'),
            'create' => CreatePartnerPayout::route('/create'),
            'edit' => EditPartnerPayout::route('/{record}/edit'),
        ];
    }
}
