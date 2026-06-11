<?php

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Part;
use App\Models\Partner;
use App\Models\PartnerPayout;
use App\Models\RepairOrder;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\FinanceReportService;
use App\Services\InvoicePdfService;
use App\Services\RepairOrderService;
use App\Services\VehicleTradingService;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

it('serves the finance panel pages to the accountant', function (string $path) {
    $accountant = User::factory()->create(['is_active' => true]);
    $accountant->assignRole('accountant');

    $this->actingAs($accountant)
        ->get($path)
        ->assertOk();
})->with([
    'dashboard' => ['/finance'],
    'invoices' => ['/finance/invoices'],
    'expenses' => ['/finance/expenses'],
    'partner payouts' => ['/finance/partner-payouts'],
    'reports' => ['/finance/reports'],
]);

it('includes a sold car and a customer repair in the period report', function () {
    Storage::fake('local');

    // Sold car → vehicle-sale invoice (margin taxation, 18'000 net)
    $vehicle = Vehicle::factory()->listed()->create();
    app(VehicleTradingService::class)->recordSale($vehicle, Customer::factory()->create(), '18000.00');

    // Customer repair → repair invoice (1h à 140 + part à 120 = 260 net, 21.06 VAT)
    $part = Part::factory()->create(['stock_qty' => 3, 'sale_price' => '120.00']);
    $order = RepairOrder::factory()->customer()->create();
    $order->tasks()->create(['description' => 'Service', 'labor_hours' => 1]);
    $order->parts()->create(['part_id' => $part->id, 'qty' => 1, 'unit_cost' => '80.00']);
    app(RepairOrderService::class)->complete($order);

    $summary = app(FinanceReportService::class)->periodSummary(now()->startOfMonth(), now()->endOfMonth());

    expect($summary['invoice_count'])->toBe(2)
        ->and($summary['revenue'])->toBe(18260.0)
        ->and($summary['vat'])->toBe(21.06)
        ->and($summary['open_amount'])->toBe(18000.0 + 281.06);
});

it('computes the quarterly VAT summary including input VAT from expenses', function () {
    Storage::fake('local');

    $part = Part::factory()->create(['stock_qty' => 3, 'sale_price' => '120.00']);
    $order = RepairOrder::factory()->customer()->create();
    $order->tasks()->create(['description' => 'Service', 'labor_hours' => 1]);
    $order->parts()->create(['part_id' => $part->id, 'qty' => 1, 'unit_cost' => '80.00']);
    app(RepairOrderService::class)->complete($order);

    Expense::factory()->create(['date' => now(), 'amount' => '1081.00', 'vat_amount' => '81.00']);

    $quarter = (int) ceil(now()->month / 3);
    $vat = app(FinanceReportService::class)->vatSummaryForQuarter(now()->year, $quarter);

    expect($vat['vat_collected'])->toBe(21.06)
        ->and($vat['vat_paid'])->toBe(81.0)
        ->and($vat['vat_due'])->toBe(round(21.06 - 81.0, 2));
});

it('exports invoices as semicolon-separated CSV', function () {
    Storage::fake('local');

    $vehicle = Vehicle::factory()->listed()->create();
    app(VehicleTradingService::class)->recordSale($vehicle, Customer::factory()->create(['name' => 'Muster; AG']), '18000.00');

    $csv = app(FinanceReportService::class)->invoicesCsv(now()->startOfMonth(), now()->endOfMonth());

    expect($csv)->toContain('Datum;Beleg-Nr.;Typ;Kunde')
        ->and($csv)->toContain('vehicle_sale')
        ->and($csv)->toContain('"Muster; AG"')
        ->and($csv)->toContain('18000.00');
});

it('generates an invoice PDF on the private disk', function () {
    Storage::fake('local');

    $invoice = Invoice::create(['type' => 'repair', 'customer_id' => Customer::factory()->create()->id]);
    $invoice->lines()->create(['description' => 'Arbeit', 'qty' => 2, 'unit_price' => '140.00', 'vat_rate' => 8.1]);
    $invoice->recalculateTotals();

    $path = app(InvoicePdfService::class)->generate($invoice);

    Storage::disk('local')->assertExists($path);
    expect($invoice->fresh()->pdf_path)->toBe($path);
});

it('marks invoices as paid and removes them from open items', function () {
    $invoice = Invoice::create(['type' => 'repair', 'status' => InvoiceStatus::Sent]);
    $invoice->lines()->create(['description' => 'Arbeit', 'qty' => 1, 'unit_price' => '100.00', 'vat_rate' => 8.1]);
    $invoice->recalculateTotals();

    $invoice->update(['status' => InvoiceStatus::Paid, 'paid_at' => now(), 'payment_method' => 'bank']);

    $summary = app(FinanceReportService::class)->periodSummary(now()->startOfMonth(), now()->endOfMonth());

    expect($summary['open_amount'])->toBe(0.0);
});

it('records partner payouts with creator and audit trail', function () {
    $accountant = User::factory()->create(['is_active' => true]);
    $accountant->assignRole('accountant');
    $partner = Partner::factory()->create();

    $this->actingAs($accountant);

    $payout = PartnerPayout::create([
        'partner_id' => $partner->id,
        'amount' => '5000.00',
        'date' => now()->toDateString(),
        'reference' => 'AUSZ-2026-001',
        'created_by' => $accountant->id,
    ]);

    expect($payout->partner->id)->toBe($partner->id)
        ->and(Activity::where('subject_type', PartnerPayout::class)->exists())->toBeTrue();
});
