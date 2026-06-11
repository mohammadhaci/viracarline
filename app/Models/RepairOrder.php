<?php

namespace App\Models;

use App\Enums\RepairOrderPriority;
use App\Enums\RepairOrderStatus;
use App\Enums\RepairOrderType;
use Database\Factories\RepairOrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairOrder extends Model
{
    /** @use HasFactory<RepairOrderFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'type',
        'vehicle_id',
        'customer_id',
        'customer_vehicle_info',
        'assigned_to',
        'status',
        'priority',
        'diagnosis',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => RepairOrderType::class,
            'status' => RepairOrderStatus::class,
            'priority' => RepairOrderPriority::class,
            'customer_vehicle_info' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            $order->number ??= self::nextNumber();
        });
    }

    public static function nextNumber(): string
    {
        $year = now()->format('Y');
        $sequence = self::where('number', 'like', "RO-{$year}-%")->count() + 1;

        return sprintf('RO-%s-%04d', $year, $sequence);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(RepairTask::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(RepairPart::class);
    }

    public function laborHours(): float
    {
        return (float) $this->tasks()->sum('labor_hours');
    }

    public function partsCost(): float
    {
        return (float) $this->parts()->selectRaw('COALESCE(SUM(qty * unit_cost), 0) as total')->value('total');
    }
}
