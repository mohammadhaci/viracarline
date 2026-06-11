<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\VehicleStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\Vehicle;
use App\Models\VehiclePurchase;
use App\Models\VehicleSale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Ankauf/Verkauf flow (plan §7 phase 7): purchase recording, the
 * publishing pipeline, sale recording with contract PDF, and margin.
 */
class VehicleTradingService
{
    /**
     * @param  array<string, mixed>  $purchaseData  seller info: customer_id|seller_name, price, purchased_at, inspection_id
     */
    public function recordPurchase(Vehicle $vehicle, array $purchaseData): VehiclePurchase
    {
        return DB::transaction(function () use ($vehicle, $purchaseData) {
            $purchase = VehiclePurchase::create([
                'vehicle_id' => $vehicle->id,
                'customer_id' => $purchaseData['customer_id'] ?? null,
                'seller_name' => $purchaseData['seller_name'] ?? null,
                'price' => $purchaseData['price'] ?? $vehicle->purchase_price,
                'purchased_at' => $purchaseData['purchased_at'] ?? $vehicle->purchase_date,
                'inspection_id' => $purchaseData['inspection_id'] ?? null,
            ]);

            $vehicle->update(['status' => VehicleStatus::Purchased]);

            return $purchase;
        });
    }

    public function recordSale(Vehicle $vehicle, Customer $customer, string $price, ?string $vatMode = null, ?\DateTimeInterface $soldAt = null): VehicleSale
    {
        return DB::transaction(function () use ($vehicle, $customer, $price, $vatMode, $soldAt) {
            $soldAt ??= now();
            $vatMode ??= 'margin';

            $sale = VehicleSale::create([
                'vehicle_id' => $vehicle->id,
                'customer_id' => $customer->id,
                'price' => $price,
                'vat_mode' => $vatMode,
                'sold_at' => $soldAt,
            ]);

            $vehicle->update([
                'status' => VehicleStatus::Sold,
                'sold_price' => $price,
                'sold_at' => $soldAt,
                'is_published' => false,
            ]);

            $sale->update(['contract_pdf_path' => $this->generateContract($sale)]);

            $this->createDraftInvoice($sale, $vatMode);

            return $sale->refresh();
        });
    }

    public function margin(Vehicle $vehicle): ?float
    {
        if ($vehicle->sold_price === null) {
            return null;
        }

        return round(
            (float) $vehicle->sold_price
            - (float) $vehicle->purchase_price
            - (float) $vehicle->costs()->sum('amount'),
            2,
        );
    }

    private function generateContract(VehicleSale $sale): string
    {
        $path = "contracts/kaufvertrag-{$sale->vehicle->slug}-{$sale->id}.pdf";

        $pdf = Pdf::loadView('pdf.contract', [
            'sale' => $sale->load(['vehicle', 'customer']),
            'companyName' => Setting::get('site_company_name', 'Vira Car Lines AG'),
            'companyAddress' => Setting::get('site_address'),
        ]);

        // Contracts live on the private disk — never under public_html (plan §6).
        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    private function createDraftInvoice(VehicleSale $sale, string $vatMode): Invoice
    {
        // Margin taxation (used vehicles): no VAT shown on the invoice.
        $vatRate = $vatMode === 'margin'
            ? 0.0
            : (float) Setting::get(RepairOrderService::VAT_RATE_KEY, RepairOrderService::DEFAULT_VAT_RATE);

        $invoice = Invoice::create([
            'type' => InvoiceType::VehicleSale,
            'status' => InvoiceStatus::Draft,
            'customer_id' => $sale->customer_id,
            'vehicle_id' => $sale->vehicle_id,
            'vat_rate' => $vatRate,
            'due_at' => now()->addDays(10),
        ]);

        $vehicle = $sale->vehicle;

        $invoice->lines()->create([
            'description' => "Fahrzeug {$vehicle->brand} {$vehicle->model} ({$vehicle->year}), VIN {$vehicle->vin}",
            'qty' => 1,
            'unit_price' => $sale->price,
            'vat_rate' => $vatRate,
        ]);

        $invoice->recalculateTotals();

        return $invoice;
    }
}
