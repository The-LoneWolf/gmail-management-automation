<?php

namespace App\Filament\Resources\EmailMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EmailMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gmailAccount.google_email')
                    ->label('Account')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sender_email')
                    ->label('Sender')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->searchable()
                    ->limit(80),
                TextColumn::make('snippet')
                    ->limit(80)
                    ->toggleable(),
                TextColumn::make('direction')
                    ->badge(),
                TextColumn::make('processing_status')
                    ->badge(),
                IconColumn::make('has_attachments')
                    ->boolean(),
                IconColumn::make('is_read')
                    ->boolean(),
                TextColumn::make('received_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('gmail_account_id')
                    ->relationship('gmailAccount', 'google_email')
                    ->label('Gmail account'),
                SelectFilter::make('direction')
                    ->options([
                        'incoming' => 'Incoming',
                        'outgoing' => 'Outgoing',
                        'draft' => 'Draft',
                        'unknown' => 'Unknown',
                    ]),
                SelectFilter::make('processing_status')
                    ->options([
                        'pending' => 'Pending',
                        'parsing' => 'Parsing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'needs_review' => 'Needs review',
                        'skipped' => 'Skipped',
                    ]),
                TernaryFilter::make('has_attachments'),
                TernaryFilter::make('is_read'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
