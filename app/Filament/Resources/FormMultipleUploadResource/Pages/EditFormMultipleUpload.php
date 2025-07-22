<?php

namespace App\Filament\Resources\FormMultipleUploadResource\Pages;

use App\Filament\Resources\FormMultipleUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFormMultipleUpload extends EditRecord
{
    protected static string $resource = FormMultipleUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
