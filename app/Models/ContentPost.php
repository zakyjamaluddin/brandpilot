<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'tiktok_post_id',
        'title', // <-- Tambahkan ini
        'media_type',
        'original_caption',
        'ai_generated_caption',
        'video_url',
        'media_gallery',
        'caption_type',
        'status',
        'scheduled_at',
        'published_at',
        'total_views',
        'total_comments',
        'platform_post_ids',
    ];

    protected $casts = [
        'platform_post_ids' => 'array',
        'media_gallery'     => 'array', 
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    // Relasi balik ke Tenant/Team (wajib untuk Filament Multi-Tenancy)
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
