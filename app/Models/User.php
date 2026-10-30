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
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasFactory, Notifiable, HasRoles;
    use HasPushSubscriptions;

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
        // Super admin melihat dan bisa berpindah ke SEMUA tenant.
        if ($this->isSuperAdmin()) {
            return Team::query()->orderBy('name')->get();
        }

        return $this->teams;
    }

    // Method wajib Filament HasTenants: Validasi kepemilikan brand
    public function canAccessTenant(Model $tenant): bool
    {
        // Super admin boleh membuka tenant mana pun, termasuk yang bukan miliknya.
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->teams->contains($tenant);
    }

    /**
     * Super admin = user #1 (akun pemilik instalasi) atau pemegang role super_admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->getKey() === 1 || $this->hasRole('super_admin');
    }

}
