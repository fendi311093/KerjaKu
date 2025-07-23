<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormMultipleUpload extends Model
{
    protected $fillable = [
        'to',
        'cc',
        'subject',
        'attachments',
        'status_sent',
        'status_received',
        'sent_at',
        'received_at'
    ];

    protected $casts = [
        'attachments' => 'array',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
        'status_sent' => 'boolean',
        'status_received' => 'boolean'
    ];
}
