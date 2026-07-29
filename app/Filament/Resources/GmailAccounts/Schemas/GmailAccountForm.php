<?php

namespace App\Filament\Resources\GmailAccounts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GmailAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('google_email')
                    ->email()
                    ->disabled(),
                Select::make('sync_status')
                    ->options([
                        'connected' => 'Connected',
                        'syncing' => 'Syncing',
                        'needs_reconnect' => 'Needs reconnect',
                        'failed' => 'Failed',
                        'disabled' => 'Disabled',
                    ])
                    ->disabled(),
                DateTimePicker::make('token_expires_at')->disabled(),
                DateTimePicker::make('last_synced_at')->disabled(),
                TextInput::make('history_id')->disabled(),
            ]);
    }
}
