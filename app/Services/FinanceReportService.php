<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Expense;
use App\Models\Invoice;
use Carbon\CarbonInterface;

class FinanceReportService
{
    /**
     * Period report (plan §3.5): revenue, VAT, and expenses between two dates.
     *
     * @return array{revenue: float, vat: float, invoice_count: int, expenses: float, expense_vat: float, open_amount: float}
     */
    public function periodSummary(CarbonInterface $from, CarbonInterface $to): array
    {
        $invoices = Invoice::query()
            ->whereNot('status', InvoiceStatus::Cancelled)
            ->whereBetween('created_at', [$from->startOfDay(), $to->copy()->endOfDay()])
            ->get();

        $expenses = Expense::query()
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get();

        return [
            'revenue' => round((float) $invoices->sum('subtotal'), 2),
            'vat' => round((float) $invoices->sum('vat_amount'), 2),
            'invoice_count' => $invoices->count(),
            'expenses' => round((float) $expenses->sum('amount'), 2),
            'expense_vat' => round((float) $expenses->sum('vat_amount'), 2),
            'open_amount' => round((float) $invoices->whereNull('paid_at')->sum('total'), 2),
        ];
    }

    /**
     * Quarterly MWST summary (plan §3.5).
     *
     * @return array{quarter: string, vat_collected: float, vat_paid: float, vat_due: float}
     */
    public function vatSummaryForQuarter(int $year, int $quarter): array
    {
        $from = now()->setDate($year, ($quarter - 1) * 3 + 1, 1)->startOfDay();
        $to = $from->copy()->addMonths(3)->subDay()->endOfDay();

        $summary = $this->periodSummary($from, $to);

        return [
            'quarter' => "Q{$quarter}/{$year}",
            'vat_collected' => $summary['vat'],
            'vat_paid' => $summary['expense_vat'],
            'vat_due' => round($summary['vat'] - $summary['expense_vat'], 2),
        ];
    }

    /**
     * CSV export of invoices in a period — Banana/Bexio-friendly columns.
     */
    public function invoicesCsv(CarbonInterface $from, CarbonInterface $to): string
    {
        $rows = [['Datum', 'Beleg-Nr.', 'Typ', 'Kunde', 'Netto', 'MWST', 'Brutto', 'Status', 'Bezahlt am']];

        Invoice::query()
            ->with('customer')
            ->whereBetween('created_at', [$from->startOfDay(), $to->copy()->endOfDay()])
            ->orderBy('created_at')
            ->each(function (Invoice $invoice) use (&$rows) {
                $rows[] = [
                    $invoice->created_at->format('d.m.Y'),
                    $invoice->number,
                    $invoice->type->value,
                    $invoice->customer?->name ?? '',
                    $invoice->subtotal,
                    $invoice->vat_amount,
                    $invoice->total,
                    $invoice->status->value,
                    $invoice->paid_at?->format('d.m.Y') ?? '',
                ];
            });

        return implode("\n", array_map(
            fn (array $row) => implode(';', array_map(
                fn ($value) => str_contains((string) $value, ';') ? '"'.str_replace('"', '""', (string) $value).'"' : (string) $value,
                $row,
            )),
            $rows,
        ))."\n";
    }
}
