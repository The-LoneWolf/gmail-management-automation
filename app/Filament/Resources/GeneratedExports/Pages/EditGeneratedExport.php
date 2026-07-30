<?php

namespace App\Filament\Resources\GeneratedExports\Pages;

use App\Filament\Resources\GeneratedExports\GeneratedExportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGeneratedExport extends EditRecord
{
    protected static string $resource = GeneratedExportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
