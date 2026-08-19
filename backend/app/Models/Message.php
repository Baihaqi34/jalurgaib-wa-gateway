<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'to',
        'message',
        'type',       // text, image, video, document
        'media_url',  // URL/path to media file
        'status',     // pending, sent, failed
        'wa_message_id',
        'sent_at',
        'error_message',
        'min_delay',
        'max_delay',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
