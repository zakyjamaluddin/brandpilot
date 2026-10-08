<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'platform',
        'composio_connection_id',
        'account_name',
        'is_connected',
        'metadata',
    ];

    protected $casts = [
        'is_connected' => 'boolean',
        'metadata' => 'array',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}