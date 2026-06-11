<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairTask extends Model
{
    protected $fillable = [
        'repair_order_id',
        'description',
        'is_done',
        'labor_hours',
    ];

    protected function casts(): array
    {
        return [
            'is_done' => 'boolean',
            'labor_hours' => 'decimal:2',
        ];
    }

    public function repairOrder(): BelongsTo
    {
        return $this->belongsTo(RepairOrder::class);
    }
}
