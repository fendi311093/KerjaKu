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
        Log::info('Attempting to send email for form ID: ' . $this->formMultipleUpload->id);

        try {
            Mail::send(new SendMultipleUploadMail($this->formMultipleUpload));

            $this->formMultipleUpload->update([
                'status_sent' => 'Delivered',
                'sent_at' => now(),
            ]);

            Log::info('Email sent successfully for form ID: ' . $this->formMultipleUpload->id);
        } catch (\Exception $e) {
            Log::error('Failed to send email for form ID: ' . $this->formMultipleUpload->id . ' due to connection error: ' . $e->getMessage());
            // Re-throw the exception so the failed() method is called
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        $this->formMultipleUpload->update([
            'status_sent' => 'Failed',
            'sent_at' => now(),
        ]);

        Log::error('Job SendEmailJob failed for form ID: ' . $this->formMultipleUpload->id . ' with error: ' . $exception->getMessage());
    }
}
