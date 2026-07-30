<?php

namespace App\Filament\Resources\ExtractionTemplates\Pages;

use App\Filament\Resources\ExtractionTemplates\ExtractionTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExtractionTemplates extends ListRecords
{
    protected static string $resource = ExtractionTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
