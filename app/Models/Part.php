<?php

namespace App\Models;

use Database\Factories\PartFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    /** @use HasFactory<PartFactory> */
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'stock_qty',
        'min_qty',
        'cost_price',
        'sale_price',
    ];

    protected function casts(): array
    {
        return [
            'stock_qty' => 'integer',
            'min_qty' => 'integer',
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
        ];
    }

    public function isLowStock(): bool
    {
        return $this->stock_qty <= $this->min_qty;
    }
}
