<?php

namespace App\Filament\Manage\Widgets;

use App\Enums\LeadStatus;
use App\Enums\VehicleStatus;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\Vehicle;
use App\Services\PartnerAmountService;
use App\Support\SwissMoney;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ManageStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Kennzahlen';

    protected function getStats(): array
    {
        $inStock = [
            VehicleStatus::Purchased,
            VehicleStatus::InWorkshop,
            VehicleStatus::Ready,
            VehicleStatus::Listed,
            VehicleStatus::Reserved,
        ];

        $stockCount = Vehicle::whereIn('status', $inStock)->count();
        $stockValue = (float) Vehicle::whereIn('status', $inStock)->sum('purchase_price');

        $soldThisMonth = Vehicle::query()
            ->where('status', VehicleStatus::Sold)
            ->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->withSum('costs', 'amount')
            ->get();

        $monthMargin = $soldThisMonth->sum(
            fn (Vehicle $vehicle) => (float) $vehicle->sold_price
                - (float) $vehicle->purchase_price
                - (float) ($vehicle->costs_sum_amount ?? 0),
        );

        $avgDaysInStock = (int) round(
            Vehicle::whereIn('status', $inStock)
                ->get()
                ->avg(fn (Vehicle $vehicle) => $vehicle->purchase_date->diffInDays(now())) ?? 0,
        );

        $service = app(PartnerAmountService::class);
        $partnerCapital = Partner::where('is_active', true)
            ->get()
            ->sum(fn (Partner $partner) => (float) $service->effectiveAmountFor($partner));

        return [
            Stat::make('Fahrzeuge an Lager', $stockCount)
                ->description('Einkaufswert '.SwissMoney::format($stockValue)),
            Stat::make('Verkäufe diesen Monat', $soldThisMonth->count())
                ->description('Marge '.SwissMoney::format($monthMargin))
                ->color($monthMargin >= 0 ? 'success' : 'danger'),
            Stat::make('Ø Standtage', $avgDaysInStock),
            Stat::make('Offene Leads', Lead::where('status', LeadStatus::New)->count()),
            Stat::make('Partner-Kapital (Anzeige)', SwissMoney::format($partnerCapital))
                ->description(Partner::where('is_active', true)->count().' aktive Partner'),
        ];
    }
}
