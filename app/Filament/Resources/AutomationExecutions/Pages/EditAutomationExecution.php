<?php

namespace App\Filament\Resources\AutomationExecutions\Pages;

use App\Filament\Resources\AutomationExecutions\AutomationExecutionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAutomationExecution extends EditRecord
{
    protected static string $resource = AutomationExecutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
