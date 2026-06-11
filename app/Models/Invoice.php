<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use LogsActivity;

    protected $fillable = [
        'number',
        'type',
        'customer_id',
        'repair_order_id',
        'vehicle_id',
        'subtotal',
        'vat_rate',
        'vat_amount',
        'total',
        'currency',
        'status',
        'due_at',
        'paid_at',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'type' => InvoiceType::class,
            'status' => InvoiceStatus::class,
            'subtotal' => 'decimal:2',
            'vat_rate' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'due_at' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $invoice) {
            $invoice->number ??= self::nextNumber();
        });
    }

    public static function nextNumber(): string
    {
        $year = now()->format('Y');
        $sequence = self::where('number', 'like', "RE-{$year}-%")->count() + 1;

        return sprintf('RE-%s-%04d', $year, $sequence);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function repairOrder(): BelongsTo
    {
        return $this->belongsTo(RepairOrder::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function recalculateTotals(): void
    {
        $lines = $this->lines()->get();

        $subtotal = $lines->sum(fn (InvoiceLine $line) => round((float) $line->qty * (float) $line->unit_price, 2));
        $vat = $lines->sum(fn (InvoiceLine $line) => round((float) $line->qty * (float) $line->unit_price * (float) $line->vat_rate / 100, 2));

        $this->update([
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'vat_amount' => number_format($vat, 2, '.', ''),
            'total' => number_format($subtotal + $vat, 2, '.', ''),
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['number', 'status', 'total', 'paid_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
