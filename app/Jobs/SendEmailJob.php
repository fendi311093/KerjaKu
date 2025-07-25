<?php

namespace App\Jobs;

use App\Mail\SendMultipleUploadMail;
use App\Models\FormMultipleUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $formMultipleUpload;

    /**
     * Create a new job instance.
     */
    public function __construct(FormMultipleUpload $formMultipleUpload)
    {
        $this->formMultipleUpload = $formMultipleUpload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::send(new SendMultipleUploadMail($this->formMultipleUpload));

        $this->formMultipleUpload->update([
            'status_sent' => 'Delivered',
            'sent_at' => now(),
        ]);
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        // Log the actual exception message for debugging
        Log::error('Email sending failed for FormMultipleUpload ID: ' . $this->formMultipleUpload->id, [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(), // Optional, but very helpful
        ]);

        $this->formMultipleUpload->update([
            'status_sent' => 'Failed',
            'sent_at' => now(),
        ]);
    }
}
