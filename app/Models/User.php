<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Maps each Filament panel id to the single role allowed to access it.
     * Role names are seeded in RoleSeeder and referenced everywhere; do not rename.
     *
     * @var array<string, string>
     */
    public const PANEL_ROLE_MAP = [
        'admin' => 'admin',
        'manage' => 'gm',
        'workshop' => 'mechanic',
        'partner' => 'partner',
        'finance' => 'accountant',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function partner(): HasOne
    {
        return $this->hasOne(Partner::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $role = self::PANEL_ROLE_MAP[$panel->getId()] ?? null;

        return $this->is_active
            && $role !== null
            && $this->hasRole($role);
    }
}
