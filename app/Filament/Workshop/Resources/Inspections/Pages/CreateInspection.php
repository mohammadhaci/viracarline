<?php

namespace App\Filament\Workshop\Resources\Inspections\Pages;

use App\Filament\Workshop\Resources\Inspections\InspectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInspection extends CreateRecord
{
    protected static string $resource = InspectionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['inspected_by'] = auth()->id();

        return $data;
    }
}
