<?php

namespace App\Filament\Resources\AiFeatureSettings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AiFeatureSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('feature')->badge()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('provider.name')->label('Provider')->searchable()->sortable(),
                TextColumn::make('model.provider_model_id')->label('Model')->searchable()->sortable(),
                TextColumn::make('temperature')->sortable(),
                TextColumn::make('max_output_tokens')->numeric()->sortable(),
                IconColumn::make('requires_json')->label('JSON')->boolean(),
                IconColumn::make('requires_tools')->label('Tools')->boolean(),
                IconColumn::make('requires_vision')->label('Vision')->boolean(),
                IconColumn::make('is_enabled')->boolean(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_enabled'),
                TernaryFilter::make('requires_tools'),
                TernaryFilter::make('requires_vision'),
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
