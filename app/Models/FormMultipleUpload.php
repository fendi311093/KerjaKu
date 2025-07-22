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
    ];

    protected $casts = [
        'attachments' => 'array',
    ];
}
