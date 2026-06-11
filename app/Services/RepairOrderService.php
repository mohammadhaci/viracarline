<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\RepairOrderStatus;
use App\Enums\RepairOrderType;
use App\Enums\VehicleCostType;
use App\Models\Invoice;
use App\Models\RepairOrder;
use App\Models\Setting;
use App\Models\VehicleCost;
use Illuminate\Support\Facades\DB;

class RepairOrderService
{
    public const HOURLY_RATE_KEY = 'workshop_hourly_rate';

    public const DEFAULT_HOURLY_RATE = '140.00';

    public const VAT_RATE_KEY = 'vat_rate_standard';

    public const DEFAULT_VAT_RATE = '8.1';

    /**
     * Completes a repair order (plan §3.3):
     * - internal → labor + parts cost is added to the vehicle's costs (affects margin)
     * - customer → a draft invoice for the accountant is generated (status → invoiced)
     */
    public function complete(RepairOrder $order): RepairOrder
    {
        return DB::transaction(function () use ($order) {
            $laborHours = $order->laborHours();
            $hourlyRate = (float) Setting::get(self::HOURLY_RATE_KEY, self::DEFAULT_HOURLY_RATE);
            $laborCost = round($laborHours * $hourlyRate, 2);
            $partsCost = round($order->partsCost(), 2);

            $order->update([
                'status' => RepairOrderStatus::Done,
                'finished_at' => now(),
            ]);

            if ($order->type === RepairOrderType::Internal && $order->vehicle_id !== null) {
                VehicleCost::create([
                    'vehicle_id' => $order->vehicle_id,
                    'type' => VehicleCostType::Repair,
                    'amount' => number_format($laborCost + $partsCost, 2, '.', ''),
                    'note' => "Werkstattauftrag {$order->number}",
                    'repair_order_id' => $order->id,
                ]);
            }

            if ($order->type === RepairOrderType::Customer) {
                $this->createDraftInvoice($order, $laborHours, $hourlyRate);

                $order->update(['status' => RepairOrderStatus::Invoiced]);
            }

            return $order->refresh();
        });
    }

    private function createDraftInvoice(RepairOrder $order, float $laborHours, float $hourlyRate): Invoice
    {
        $vatRate = (float) Setting::get(self::VAT_RATE_KEY, self::DEFAULT_VAT_RATE);

        $invoice = Invoice::create([
            'type' => InvoiceType::Repair,
            'status' => InvoiceStatus::Draft,
            'customer_id' => $order->customer_id,
            'repair_order_id' => $order->id,
            'vehicle_id' => $order->vehicle_id,
            'vat_rate' => $vatRate,
            'due_at' => now()->addDays(30),
        ]);

        if ($laborHours > 0) {
            $invoice->lines()->create([
                'description' => "Arbeit ({$order->number})",
                'qty' => $laborHours,
                'unit_price' => number_format($hourlyRate, 2, '.', ''),
                'vat_rate' => $vatRate,
            ]);
        }

        foreach ($order->parts()->with('part')->get() as $repairPart) {
            $invoice->lines()->create([
                'description' => $repairPart->part->name.' ('.$repairPart->part->sku.')',
                'qty' => $repairPart->qty,
                // Customers pay the sale price; unit_cost stays internal.
                'unit_price' => $repairPart->part->sale_price,
                'vat_rate' => $vatRate,
            ]);
        }

        $invoice->recalculateTotals();

        return $invoice;
    }
}
