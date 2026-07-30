<?php

namespace App\Filament\Resources\GoogleOAuthConfigurations\Pages;

use App\Filament\Resources\GoogleOAuthConfigurations\GoogleOAuthConfigurationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGoogleOAuthConfiguration extends EditRecord
{
    protected static string $resource = GoogleOAuthConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
