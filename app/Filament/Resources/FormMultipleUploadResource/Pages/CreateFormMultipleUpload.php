<?php

namespace App\Filament\Resources\FormMultipleUploadResource\Pages;

use App\Filament\Resources\FormMultipleUploadResource;
use App\Jobs\SendEmailJob;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFormMultipleUpload extends CreateRecord
{
    protected static string $resource = FormMultipleUploadResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        SendEmailJob::dispatch($this->record);
    }
}
