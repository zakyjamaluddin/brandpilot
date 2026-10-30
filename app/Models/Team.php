<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    // app/Models/Team.php
    // Tambahkan kolom baru ke $fillable di Team.php:
    protected $fillable = [
        'name',
        'slug',
        'tiktok_handle',
        'tiktok_profile_url',
        'tiktok_ownership_confirmed',
        'last_tiktok_post_id',
        'composio_api_key_id',
        'gemini_api_key_id',
        'apify_api_key_id',
        'apify_token',
        'subscription_status',
        'subscription_expires_at',
        'customer_email',
    ];

    // Tambahkan relasi ini di dalam class Team:
    public function composioKey()
    {
        return $this->belongsTo(ApiKey::class, 'composio_api_key_id');
    }

    public function geminiKey()
    {
        return $this->belongsTo(ApiKey::class, 'gemini_api_key_id');
    }

    public function apifyKey()
    {
        return $this->belongsTo(ApiKey::class, 'apify_api_key_id');
    }

    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'apify_token' => 'encrypted',
    ];

    /**
     * Token Apify efektif: token milik tenant di Setting, atau key lama yang di-assign admin.
     */
    public function apifyToken(): string
    {
        return trim((string) ($this->apify_token ?: $this->apifyKey?->api_key));
    }

    public function hasApifyToken(): bool
    {
        return $this->apifyToken() !== '';
    }

    // Relasi ke User
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    // Relasi ke Pengaturan
    public function setting(): HasOne
    {
        return $this->hasOne(TenantSetting::class);
    }

    // Relasi ke Konten
    public function contentPosts(): HasMany
    {
        return $this->hasMany(ContentPost::class);
    }

    /** @return HasMany<\App\Models\Role, self> */
    public function roles(): HasMany
    {
        return $this->hasMany(\App\Models\Role::class);
    }


    /** @return HasMany<\App\Models\InboxThread, self> */
    public function inboxThreads(): HasMany
    {
        return $this->hasMany(\App\Models\InboxThread::class);
    }

}
