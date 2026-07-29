<?php

namespace App\Filament\Resources\EmailThreads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EmailThreadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gmailAccount.google_email')
                    ->label('Account')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->searchable()
                    ->limit(80),
                TextColumn::make('message_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('priority')
                    ->badge()
                    ->placeholder('None'),
                IconColumn::make('requires_reply')
                    ->boolean(),
                IconColumn::make('requires_human_review')
                    ->boolean(),
                TextColumn::make('last_message_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('gmail_account_id')
                    ->relationship('gmailAccount', 'google_email')
                    ->label('Gmail account'),
                SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),
                TernaryFilter::make('requires_reply'),
                TernaryFilter::make('requires_human_review'),
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
