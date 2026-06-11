<?php

namespace App\Filament\Manage\Resources\Vehicles\Pages;

use App\Filament\Manage\Resources\Vehicles\VehicleResource;
use App\Services\VehicleTradingService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] ??= Str::slug("{$data['brand']} {$data['model']} {$data['year']}").'-'.Str::lower(Str::random(6));
        $data['mileage_km'] ??= 0;
        $data['fuel'] ??= 'petrol';
        $data['transmission'] ??= 'manual';

        return $data;
    }

    protected function afterCreate(): void
    {
        app(VehicleTradingService::class)->recordPurchase($this->getRecord(), []);
    }
}
