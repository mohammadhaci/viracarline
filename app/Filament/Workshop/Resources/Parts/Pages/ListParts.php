<?php

namespace App\Filament\Workshop\Resources\Parts\Pages;

use App\Filament\Workshop\Resources\Parts\PartResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParts extends ListRecords
{
    protected static string $resource = PartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
