<?php

namespace App\Filament\Resources\EmailExtractions\Pages;

use App\Filament\Resources\EmailExtractions\EmailExtractionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmailExtractions extends ListRecords
{
    protected static string $resource = EmailExtractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
