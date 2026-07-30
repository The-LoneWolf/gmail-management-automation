<?php

namespace App\Filament\Resources\EmailExtractions\Pages;

use App\Filament\Resources\EmailExtractions\EmailExtractionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmailExtraction extends EditRecord
{
    protected static string $resource = EmailExtractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
