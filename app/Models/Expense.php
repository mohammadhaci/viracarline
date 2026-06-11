<?php

namespace App\Models;

use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Expense extends Model implements HasMedia
{
    /** @use HasFactory<ExpenseFactory> */
    use HasFactory, InteractsWithMedia;

    public const CATEGORIES = [
        'vehicle_purchase' => 'Fahrzeugeinkauf',
        'parts' => 'Ersatzteile',
        'rent' => 'Miete',
        'insurance' => 'Versicherung',
        'marketing' => 'Marketing',
        'other' => 'Übrige',
    ];

    protected $fillable = [
        'category',
        'amount',
        'vat_amount',
        'date',
        'vendor',
        'vehicle_id',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'date' => 'date',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function registerMediaCollections(): void
    {
        // Receipts are private documents (plan §6).
        $this->addMediaCollection('receipt')->useDisk('local')->singleFile();
    }
}
