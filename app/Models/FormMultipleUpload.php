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
    ];

    protected static function booted()
    {
        parent::booted();

        static::created(function ($model) {

            // Dispatch the email job after creating the record
            dispatch((new SendEmailJob($model))->delay(now()->addMinutes(2)));
            // Log::info("Email job dispatched for model ID: {$moodel->id}");
        });

        static::updated(function ($model) {
            if ($model->isDirty('attachments')) {
                $originalAttachments = $model->getOriginal('attachments');

                if (!empty($originalAttachments)) {
                    static::deleteAttachments($originalAttachments);
                }
            }

            // Cek jika status diubah menjadi 'Re-Send'
            if ($model->isDirty('status_sent') && $model->status_sent === 'Re-Send') {
                // Kirim ulang email jika statusnya 'Re-Send'
                dispatch((new SendEmailJob($model))->delay(now()->addMinutes(2)));
            }
        });

        static::deleted(function ($model) {
            static::deleteAttachments($model->attachments);
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
