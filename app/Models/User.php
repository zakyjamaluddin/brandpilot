<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles; // Persiapan untuk Shield nanti

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Akses ke panel filament
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    // Relasi ke Team
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    // Method wajib Filament HasTenants: Mengambil daftar brand milik user
    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }

    // Method wajib Filament HasTenants: Validasi kepemilikan brand
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams->contains($tenant);
    }

}
