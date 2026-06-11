<?php

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\RepairOrderStatus;
use App\Models\Invoice;
use App\Models\Part;
use App\Models\RepairOrder;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\RepairOrderService;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

it('generates sequential repair order numbers per year', function () {
    $first = RepairOrder::factory()->create();
    $second = RepairOrder::factory()->create();

    $year = now()->format('Y');

    expect($first->number)->toBe("RO-{$year}-0001")
        ->and($second->number)->toBe("RO-{$year}-0002");
});

it('decrements part stock when consumed and restores it on removal', function () {
    $part = Part::factory()->create(['stock_qty' => 10]);
    $order = RepairOrder::factory()->create();

    $repairPart = $order->parts()->create(['part_id' => $part->id, 'qty' => 3, 'unit_cost' => '50.00']);
    expect($part->fresh()->stock_qty)->toBe(7);

    $repairPart->update(['qty' => 5]);
    expect($part->fresh()->stock_qty)->toBe(5);

    $repairPart->delete();
    expect($part->fresh()->stock_qty)->toBe(10);
});

it('flags parts at or below minimum stock', function () {
    expect(Part::factory()->create(['stock_qty' => 2, 'min_qty' => 3])->isLowStock())->toBeTrue()
        ->and(Part::factory()->create(['stock_qty' => 9, 'min_qty' => 3])->isLowStock())->toBeFalse();
});

it('adds labor and parts cost to the vehicle when an internal order completes', function () {
    Setting::set(RepairOrderService::HOURLY_RATE_KEY, '140.00');

    $vehicle = Vehicle::factory()->create();
    $part = Part::factory()->create(['stock_qty' => 10, 'cost_price' => '80.00']);
    $order = RepairOrder::factory()->create(['vehicle_id' => $vehicle->id]);

    $order->tasks()->create(['description' => 'Bremsen ersetzen', 'labor_hours' => 2.5]);
    $order->parts()->create(['part_id' => $part->id, 'qty' => 2, 'unit_cost' => '80.00']);

    app(RepairOrderService::class)->complete($order);

    // 2.5h × 140 + 2 × 80 = 510.00
    $cost = $vehicle->costs()->first();

    expect($order->fresh()->status)->toBe(RepairOrderStatus::Done)
        ->and($cost)->not->toBeNull()
        ->and($cost->amount)->toBe('510.00')
        ->and($cost->repair_order_id)->toBe($order->id)
        ->and((float) $vehicle->costs()->sum('amount'))->toBe(510.0);
});

it('creates a draft invoice with VAT when a customer order completes', function () {
    Setting::set(RepairOrderService::HOURLY_RATE_KEY, '140.00');
    Setting::set(RepairOrderService::VAT_RATE_KEY, '8.1');

    $part = Part::factory()->create(['stock_qty' => 5, 'cost_price' => '80.00', 'sale_price' => '120.00']);
    $order = RepairOrder::factory()->customer()->create();

    $order->tasks()->create(['description' => 'Service', 'labor_hours' => 1]);
    $order->parts()->create(['part_id' => $part->id, 'qty' => 1, 'unit_cost' => '80.00']);

    app(RepairOrderService::class)->complete($order);

    $invoice = Invoice::where('repair_order_id', $order->id)->first();

    // Subtotal 140 + 120 = 260.00; VAT 8.1% = 21.06; total 281.06
    expect($order->fresh()->status)->toBe(RepairOrderStatus::Invoiced)
        ->and($invoice)->not->toBeNull()
        ->and($invoice->status)->toBe(InvoiceStatus::Draft)
        ->and($invoice->type)->toBe(InvoiceType::Repair)
        ->and($invoice->customer_id)->toBe($order->customer_id)
        ->and($invoice->lines)->toHaveCount(2)
        ->and($invoice->subtotal)->toBe('260.00')
        ->and($invoice->vat_amount)->toBe('21.06')
        ->and($invoice->total)->toBe('281.06');
});

it('does not invoice internal orders', function () {
    $order = RepairOrder::factory()->create();
    $order->tasks()->create(['description' => 'Aufbereitung', 'labor_hours' => 1]);

    app(RepairOrderService::class)->complete($order);

    expect(Invoice::count())->toBe(0);
});

it('serves the workshop panel pages to mechanics', function (string $path) {
    $mechanic = User::factory()->create(['is_active' => true]);
    $mechanic->assignRole('mechanic');

    $this->actingAs($mechanic)
        ->get($path)
        ->assertOk();
})->with([
    'dashboard' => ['/workshop'],
    'repair orders' => ['/workshop/repair-orders'],
    'parts' => ['/workshop/parts'],
    'inspections' => ['/workshop/inspections'],
]);
