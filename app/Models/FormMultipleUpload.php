<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
use Illuminate\Database\Eloquent\Model;

class FormMultipleUpload extends Model
{
    protected $fillable = [
        'to',
        'cc',
        'subject',
        'attachments',
        'status_sent',
        'sent_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'sent_at' => 'datetime',
        'status_sent' => 'boolean',
    ];

    protected static function booted()
    {
        parent::booted();

        static::created(function ($moodel) {

            // Dispatch the email job after creating the record
            dispatch((new SendEmailJob($moodel))->delay(now()->addMinutes(2)));
            // Log::info("Email job dispatched for model ID: {$moodel->id}");
        });

        static::updated(function ($model) {
            if ($model->isDirty('attachments')) {
                $originalAttachments = $model->getOriginal('attachments');

                if (!empty($originalAttachments)) {
                    static::deleteAttachments($originalAttachments);
                }
            }
        });
    }

    // Hapus attachments dari storage
    protected static function deleteAttachments($attachments)
    {
        if (!$attachments) {
            return;
        }

        $fileLocations = is_array($attachments) ? $attachments : json_decode($attachments, true);
        if (!is_array($fileLocations)) return;

        foreach ($fileLocations as $fileLocation) {
            $fullPath = public_path('storage/' . $fileLocation);
            if (file_exists($fullPath)) {
                try {
                    unlink($fullPath);
                    // Log::info("File deleted: $fileLocation");
                } catch (\Exception $e) {
                    // Log::error("Failed to delete $fileLocation: " . $e->getMessage());
                }
            }
        }
    }
}
