<?php

namespace App\Filament\Resources\EmailClassifications\Pages;

use App\Filament\Resources\EmailClassifications\EmailClassificationResource;
use Filament\Resources\Pages\ListRecords;

class ListEmailClassifications extends ListRecords
{
    protected static string $resource = EmailClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
