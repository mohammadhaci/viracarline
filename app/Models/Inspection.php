<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Inspection extends Model implements HasMedia
{
    use InteractsWithMedia;

    /** Standard purchase-inspection checklist areas (plan §3.3). */
    public const CHECKLIST_ITEMS = [
        'body' => 'Karosserie',
        'engine' => 'Motor',
        'electronics' => 'Elektronik',
        'tires' => 'Reifen',
        'interior' => 'Innenraum',
        'accident_history' => 'Unfallhistorie',
    ];

    protected $fillable = [
        'vehicle_id',
        'checklist',
        'result',
        'note',
        'inspected_by',
    ];

    protected function casts(): array
    {
        return [
            'checklist' => 'array',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos');
    }
}
