<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiclePurchase extends Model
{
    protected $fillable = [
        'vehicle_id',
        'customer_id',
        'seller_name',
        'price',
        'purchased_at',
        'inspection_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'purchased_at' => 'date',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }
}
