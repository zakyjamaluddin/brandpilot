<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InboxThread extends Model
{
    use HasFactory;

    // app/Models/InboxThread.php
    protected $fillable = [
        'team_id',
        'platform',
        'type',
        'external_id',
        'sender_name',
        'sender_avatar',
        'last_snippet',
        'last_interaction_at',
        'is_replied',  // <-- Tambahkan
        'replied_at',  // <-- Tambahkan
    ];

    protected $casts = [
        'last_interaction_at' => 'datetime',
        'is_replied'          => 'boolean',
        'replied_at'          => 'datetime',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(InboxMessage::class);
    }
}
