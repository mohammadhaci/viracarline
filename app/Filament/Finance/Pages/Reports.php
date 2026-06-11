<?php

namespace App\Filament\Finance\Pages;

use App\Services\FinanceReportService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Reports extends Page
{
    protected string $view = 'filament.finance.pages.reports';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $title = 'Berichte & MWST';

    public int $year;

    public int $quarter;

    public function mount(): void
    {
        $this->year = now()->year;
        $this->quarter = (int) ceil(now()->month / 3);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $service = app(FinanceReportService::class);

        $from = now()->setDate($this->year, ($this->quarter - 1) * 3 + 1, 1)->startOfDay();
        $to = $from->copy()->addMonths(3)->subDay();

        return [
            'summary' => $service->periodSummary($from, $to),
            'vat' => $service->vatSummaryForQuarter($this->year, $this->quarter),
            'years' => range(now()->year - 3, now()->year),
        ];
    }

    public function exportCsv(): StreamedResponse
    {
        $from = now()->setDate($this->year, ($this->quarter - 1) * 3 + 1, 1)->startOfDay();
        $to = $from->copy()->addMonths(3)->subDay();

        $csv = app(FinanceReportService::class)->invoicesCsv($from, $to);

        return response()->streamDownload(
            fn () => print ($csv),
            "rechnungen-q{$this->quarter}-{$this->year}.csv",
            ['Content-Type' => 'text/csv; charset=UTF-8'],
        );
    }
}
