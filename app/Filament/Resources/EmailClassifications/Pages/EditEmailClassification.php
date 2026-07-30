<?php

namespace App\Filament\Resources\EmailClassifications\Pages;

use App\Filament\Resources\EmailClassifications\EmailClassificationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmailClassification extends EditRecord
{
    protected static string $resource = EmailClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
