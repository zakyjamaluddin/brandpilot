<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'label',
        'api_key',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Team yang menggunakan key ini.
     * Menggunakan default handler agar kebal terhadap nilai NULL saat query builder diinisialisasi.
     */
    public function assignedTeam(): HasOne
    {
        return match ($this->provider) {
            'composio' => $this->hasOne(Team::class, 'composio_api_key_id'),
            'gemini'   => $this->hasOne(Team::class, 'gemini_api_key_id'),
            'apify'    => $this->hasOne(Team::class, 'apify_api_key_id'),
            default    => $this->hasOne(Team::class, 'composio_api_key_id')->whereRaw('1 = 0'),
        };
    }
}
