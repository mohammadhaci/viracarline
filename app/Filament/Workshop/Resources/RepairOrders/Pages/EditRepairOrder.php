<?php

namespace App\Filament\Workshop\Resources\RepairOrders\Pages;

use App\Enums\RepairOrderStatus;
use App\Filament\Workshop\Resources\RepairOrders\RepairOrderResource;
use App\Services\RepairOrderService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditRepairOrder extends EditRecord
{
    protected static string $resource = RepairOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('complete')
                ->label('Abschliessen')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => ! in_array($this->getRecord()->status, [RepairOrderStatus::Done, RepairOrderStatus::Invoiced], true))
                ->action(function () {
                    app(RepairOrderService::class)->complete($this->getRecord());

                    Notification::make()
                        ->success()
                        ->title('Auftrag abgeschlossen')
                        ->send();

                    $this->refreshFormData(['status', 'finished_at']);
                }),
            DeleteAction::make(),
        ];
    }
}
