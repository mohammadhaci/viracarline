<?php

namespace App\Filament\Workshop\Resources\Inspections\Pages;

use App\Filament\Workshop\Resources\Inspections\InspectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInspection extends EditRecord
{
    protected static string $resource = InspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
