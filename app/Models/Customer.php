<?php

namespace App\Models;

use App\Enums\CustomerType;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'contact',
        'address',
        'language',
    ];

    protected function casts(): array
    {
        return [
            'type' => CustomerType::class,
            'contact' => 'array',
        ];
    }
}
