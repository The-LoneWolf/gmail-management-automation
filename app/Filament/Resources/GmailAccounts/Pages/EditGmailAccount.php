<?php

namespace App\Filament\Resources\GmailAccounts\Pages;

use App\Filament\Resources\GmailAccounts\GmailAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGmailAccount extends EditRecord
{
    protected static string $resource = GmailAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
