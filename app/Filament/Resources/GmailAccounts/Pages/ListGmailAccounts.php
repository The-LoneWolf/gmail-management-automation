<?php

namespace App\Filament\Resources\GmailAccounts\Pages;

use App\Filament\Resources\GmailAccounts\GmailAccountResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListGmailAccounts extends ListRecords
{
    protected static string $resource = GmailAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('connect_gmail')
                ->label('Connect Gmail')
                ->icon('heroicon-o-link')
                ->url(route('gmail.oauth.redirect')),
        ];
    }
}
