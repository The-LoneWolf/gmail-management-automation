<?php

namespace App\Filament\Resources\AiFeatureSettings\Pages;

use App\Filament\Resources\AiFeatureSettings\AiFeatureSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAiFeatureSetting extends EditRecord
{
    protected static string $resource = AiFeatureSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
