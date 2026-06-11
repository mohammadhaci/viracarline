<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoicePdfService
{
    /**
     * Renders the invoice PDF to the private disk and stores the path.
     */
    public function generate(Invoice $invoice): string
    {
        $path = "invoices/{$invoice->number}.pdf";

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice->load(['lines', 'customer']),
            'companyName' => Setting::get('site_company_name', 'Vira Car Lines AG'),
            'companyAddress' => Setting::get('site_address'),
        ]);

        Storage::disk('local')->put($path, $pdf->output());

        $invoice->update(['pdf_path' => $path]);

        return $path;
    }
}
