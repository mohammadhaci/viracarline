<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairPart extends Model
{
    protected $fillable = [
        'repair_order_id',
        'part_id',
        'qty',
        'unit_cost',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_cost' => 'decimal:2',
        ];
    }

    /**
     * Consuming a part decrements stock; corrections restore it (plan §3.3).
     */
    protected static function booted(): void
    {
        static::created(function (self $repairPart) {
            $repairPart->part()->decrement('stock_qty', $repairPart->qty);
        });

        static::updated(function (self $repairPart) {
            $delta = $repairPart->qty - $repairPart->getOriginal('qty');

            if ($delta !== 0) {
                $repairPart->part()->decrement('stock_qty', $delta);
            }
        });

        static::deleted(function (self $repairPart) {
            $repairPart->part()->increment('stock_qty', $repairPart->qty);
        });
    }

    public function repairOrder(): BelongsTo
    {
        return $this->belongsTo(RepairOrder::class);
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }
}
