<?php

namespace App\Filament\Resources\AiFeatureSettings\Pages;

use App\Filament\Resources\AiFeatureSettings\AiFeatureSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAiFeatureSettings extends ListRecords
{
    protected static string $resource = AiFeatureSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
