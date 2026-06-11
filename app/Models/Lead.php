<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Lead extends Model implements HasMedia
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'type',
        'vehicle_id',
        'name',
        'email',
        'phone',
        'message',
        'locale',
        'status',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'type' => LeadType::class,
            'status' => LeadStatus::class,
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
