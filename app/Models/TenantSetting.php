<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'schedule_mode',
        'dynamic_delay_minutes',
        'static_posting_hours',
        'auto_caption_enabled',
        'custom_caption_prompt',
        'auto_reply_enabled',
        'brand_knowledge_base',
    ];

    protected $casts = [
        'static_posting_hours' => 'array',
        'auto_caption_enabled' => 'boolean',
        'auto_reply_enabled' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}