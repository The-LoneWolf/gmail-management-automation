<?php

namespace App\Filament\Resources\GmailAccounts\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GmailAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('google_email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sync_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('last_synced_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('token_expires_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('sync_error')
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('sync_status')
                    ->options([
                        'connected' => 'Connected',
                        'syncing' => 'Syncing',
                        'needs_reconnect' => 'Needs reconnect',
                        'failed' => 'Failed',
                        'disabled' => 'Disabled',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('connect_gmail')
                    ->label('Connect Gmail')
                    ->icon('heroicon-o-link')
                    ->url(route('gmail.oauth.redirect')),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
