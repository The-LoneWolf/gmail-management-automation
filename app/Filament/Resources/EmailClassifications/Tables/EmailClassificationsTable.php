<?php

namespace App\Filament\Resources\EmailClassifications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EmailClassificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('message.sender_email')
                    ->label('Sender')
                    ->searchable(),
                TextColumn::make('message.subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(70),
                TextColumn::make('summary')->limit(80),
                TextColumn::make('suggestedState.name')->label('State')->badge(),
                TextColumn::make('priority')->badge()->sortable(),
                TextColumn::make('sentiment')->badge()->sortable(),
                IconColumn::make('requires_reply')->boolean(),
                IconColumn::make('requires_human_review')->boolean(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'needs_review' => 'Needs review',
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
