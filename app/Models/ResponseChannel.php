<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponseChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'response_id',
        'provider',
        'provider_message_id',
        'payload_json',
        'processed_status',
        'received_at',
        'processed_at',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * Parent response relation.
     */
    public function response(): BelongsTo
    {
        return $this->belongsTo(Response::class);
    }
}
