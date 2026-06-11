<?php

namespace App\Filament\Workshop\Resources\RepairOrders\Pages;

use App\Filament\Workshop\Resources\RepairOrders\RepairOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRepairOrders extends ListRecords
{
    protected static string $resource = RepairOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
