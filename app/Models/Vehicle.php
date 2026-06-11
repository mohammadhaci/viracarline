<?php

namespace App\Models;

use App\Enums\VehicleStatus;
use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Vehicle extends Model implements HasMedia
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory, HasTranslations, InteractsWithMedia, LogsActivity;

    /** @var list<string> */
    public array $translatable = ['title', 'description'];

    protected $fillable = [
        'vin',
        'brand',
        'model',
        'variant',
        'year',
        'mileage_km',
        'fuel',
        'transmission',
        'color',
        'purchase_price',
        'purchase_date',
        'purchase_source',
        'asking_price',
        'sold_price',
        'sold_at',
        'status',
        'partner_id',
        'is_published',
        'is_featured',
        'show_price',
        'title',
        'description',
        'slug',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'mileage_km' => 'integer',
            'purchase_price' => 'decimal:2',
            'purchase_date' => 'date',
            'asking_price' => 'decimal:2',
            'sold_price' => 'decimal:2',
            'sold_at' => 'datetime',
            'status' => VehicleStatus::class,
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'show_price' => 'boolean',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function costs(): HasMany
    {
        return $this->hasMany(VehicleCost::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['purchase_price', 'asking_price', 'sold_price', 'status', 'partner_id', 'is_published'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
