<?php

namespace App\Filament\Resources\ExtractionTemplates\Pages;

use App\Filament\Resources\ExtractionTemplates\ExtractionTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExtractionTemplate extends EditRecord
{
    protected static string $resource = ExtractionTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
