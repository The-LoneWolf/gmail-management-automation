<?php

namespace App\Filament\Resources\AiModels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AiModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider.name')->label('Provider')->searchable()->sortable(),
                TextColumn::make('provider_model_id')->label('Model ID')->searchable()->sortable(),
                TextColumn::make('max_input_tokens')->numeric()->sortable(),
                TextColumn::make('max_output_tokens')->numeric()->sortable(),
                IconColumn::make('supports_tool_calling')->label('Tools')->boolean(),
                IconColumn::make('supports_vision')->label('Vision')->boolean(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
                TernaryFilter::make('supports_tool_calling'),
                TernaryFilter::make('supports_vision'),
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
