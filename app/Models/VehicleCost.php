<?php

namespace App\Models;

use App\Enums\VehicleCostType;
use Database\Factories\VehicleCostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleCost extends Model
{
    /** @use HasFactory<VehicleCostFactory> */
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'type',
        'amount',
        'note',
        'repair_order_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => VehicleCostType::class,
            'amount' => 'decimal:2',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
