<?php

namespace App\Filament\Resources\GoogleOAuthConfigurations\Pages;

use App\Filament\Resources\GoogleOAuthConfigurations\GoogleOAuthConfigurationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGoogleOAuthConfigurations extends ListRecords
{
    protected static string $resource = GoogleOAuthConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
