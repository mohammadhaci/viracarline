<?php

use App\Enums\InvoiceType;
use App\Enums\VehicleStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Part;
use App\Models\RepairOrder;
use App\Models\Vehicle;
use App\Models\VehiclePurchase;
use App\Models\VehicleSale;
use App\Services\RepairOrderService;
use App\Services\VehicleTradingService;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->trading = app(VehicleTradingService::class);
});

it('records a purchase and links it to the vehicle', function () {
    $vehicle = Vehicle::factory()->create(['purchase_price' => '15000.00']);
    $seller = Customer::factory()->create();

    $purchase = $this->trading->recordPurchase($vehicle, [
        'customer_id' => $seller->id,
        'price' => '15000.00',
        'purchased_at' => now()->toDateString(),
    ]);

    expect($purchase->vehicle_id)->toBe($vehicle->id)
        ->and($purchase->price)->toBe('15000.00')
        ->and($vehicle->fresh()->status)->toBe(VehicleStatus::Purchased);
});

it('runs the full lifecycle of one car with correct margin', function () {
    Storage::fake('local');

    // 1. Ankauf
    $vehicle = Vehicle::factory()->create([
        'purchase_price' => '15000.00',
        'status' => VehicleStatus::Purchased,
    ]);
    $this->trading->recordPurchase($vehicle, ['seller_name' => 'Privat', 'price' => '15000.00', 'purchased_at' => now()->toDateString()]);

    // 2. Werkstatt: Aufbereitung kostet 2h à 140 + 1 Teil à 160 = 440.00
    $part = Part::factory()->create(['stock_qty' => 4, 'cost_price' => '160.00']);
    $order = RepairOrder::factory()->create(['vehicle_id' => $vehicle->id]);
    $order->tasks()->create(['description' => 'Aufbereitung', 'labor_hours' => 2]);
    $order->parts()->create(['part_id' => $part->id, 'qty' => 1, 'unit_cost' => '160.00']);
    app(RepairOrderService::class)->complete($order);

    // 3. Publizieren
    $vehicle->update(['status' => VehicleStatus::Listed, 'is_published' => true, 'asking_price' => '19500.00']);

    // 4. Verkauf
    $buyer = Customer::factory()->create();
    $sale = $this->trading->recordSale($vehicle, $buyer, '19000.00');

    $vehicle->refresh();

    // Margin: 19000 - 15000 - 440 = 3560.00
    expect($vehicle->status)->toBe(VehicleStatus::Sold)
        ->and($vehicle->sold_price)->toBe('19000.00')
        ->and($vehicle->is_published)->toBeFalse()
        ->and($this->trading->margin($vehicle))->toBe(3560.0)
        ->and($sale->contract_pdf_path)->not->toBeNull();

    Storage::disk('local')->assertExists($sale->contract_pdf_path);
});

it('creates a margin-taxation draft invoice for a vehicle sale', function () {
    Storage::fake('local');

    $vehicle = Vehicle::factory()->listed()->create();
    $buyer = Customer::factory()->create();

    $this->trading->recordSale($vehicle, $buyer, '22000.00', 'margin');

    $invoice = Invoice::where('vehicle_id', $vehicle->id)->where('type', InvoiceType::VehicleSale)->first();

    expect($invoice)->not->toBeNull()
        ->and($invoice->subtotal)->toBe('22000.00')
        ->and($invoice->vat_amount)->toBe('0.00')
        ->and($invoice->total)->toBe('22000.00')
        ->and($invoice->customer_id)->toBe($buyer->id);
});

it('applies standard VAT on the sale invoice when requested', function () {
    Storage::fake('local');

    $vehicle = Vehicle::factory()->listed()->create();
    $buyer = Customer::factory()->create();

    $this->trading->recordSale($vehicle, $buyer, '10000.00', 'standard');

    $invoice = Invoice::where('vehicle_id', $vehicle->id)->first();

    // 10000 × 8.1% = 810.00
    expect($invoice->vat_amount)->toBe('810.00')
        ->and($invoice->total)->toBe('10810.00');
});

it('keeps the sale history per vehicle', function () {
    Storage::fake('local');

    $vehicle = Vehicle::factory()->listed()->create();
    $this->trading->recordSale($vehicle, Customer::factory()->create(), '18000.00');

    expect(VehicleSale::where('vehicle_id', $vehicle->id)->count())->toBe(1)
        ->and(VehiclePurchase::count())->toBe(0);
});
