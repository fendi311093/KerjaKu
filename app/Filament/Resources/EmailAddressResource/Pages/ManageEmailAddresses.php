<?php

namespace App\Filament\Resources\EmailAddressResource\Pages;

use App\Filament\Resources\EmailAddressResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEmailAddresses extends ManageRecords
{
    protected static string $resource = EmailAddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Email')
                ->icon('heroicon-o-plus')
                ->modalHeading('')
                ->modalWidth('sm')
        ];
    }
}
