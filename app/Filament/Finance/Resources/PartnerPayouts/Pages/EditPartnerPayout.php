<?php

namespace App\Filament\Finance\Resources\PartnerPayouts\Pages;

use App\Filament\Finance\Resources\PartnerPayouts\PartnerPayoutResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPartnerPayout extends EditRecord
{
    protected static string $resource = PartnerPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
