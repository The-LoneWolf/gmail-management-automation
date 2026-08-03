<?php

namespace App\Filament\Resources\AiProviders\Pages;

use App\Filament\Resources\AiProviders\AiProviderResource;
use App\Services\Ai\AiProviderPresetService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAiProviders extends ListRecords
{
    protected static string $resource = AiProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('installOpenCodeMimo')
                ->label('Install OpenCode Mimo preset')
                ->action(function (): void {
                    app(AiProviderPresetService::class)->upsertOpenCodeMimo();

                    Notification::make()
                        ->title('OpenCode Mimo preset saved')
                        ->body('The OpenCode Zen provider and mimo-v2.5-free model are ready under AI Providers and AI Models.')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
