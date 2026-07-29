<?php

namespace App\Filament\Resources\EmailThreads\Pages;

use App\Filament\Resources\EmailThreads\EmailThreadResource;
use Filament\Resources\Pages\ListRecords;

class ListEmailThreads extends ListRecords
{
    protected static string $resource = EmailThreadResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
