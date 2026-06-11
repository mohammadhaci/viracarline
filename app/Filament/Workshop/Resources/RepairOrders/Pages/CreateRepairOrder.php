<?php

namespace App\Filament\Workshop\Resources\RepairOrders\Pages;

use App\Filament\Workshop\Resources\RepairOrders\RepairOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRepairOrder extends CreateRecord
{
    protected static string $resource = RepairOrderResource::class;
}
