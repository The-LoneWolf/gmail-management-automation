<?php

namespace App\Filament\Resources\EmailThreads\Pages;

use App\Filament\Resources\EmailThreads\EmailThreadResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmailThread extends EditRecord
{
    protected static string $resource = EmailThreadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
