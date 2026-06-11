<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\FinancePanelProvider;
use App\Providers\Filament\ManagePanelProvider;
use App\Providers\Filament\PartnerPanelProvider;
use App\Providers\Filament\WorkshopPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    FinancePanelProvider::class,
    ManagePanelProvider::class,
    PartnerPanelProvider::class,
    WorkshopPanelProvider::class,
];
