<?php

namespace App\Jobs;

use App\Mail\SendMultipleUploadMail;
use App\Models\FormMultipleUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
    }
}
