<?php

namespace App\Filament\Resources\EmailMessages\Pages;

use App\Filament\Resources\EmailMessages\EmailMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmailMessage extends EditRecord
{
    protected static string $resource = EmailMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
