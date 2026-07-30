<?php

namespace App\Filament\Resources\AutomationExecutions\Pages;

use App\Filament\Resources\AutomationExecutions\AutomationExecutionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAutomationExecutions extends ListRecords
{
    protected static string $resource = AutomationExecutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
