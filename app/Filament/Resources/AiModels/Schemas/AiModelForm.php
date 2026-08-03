<?php

namespace App\Filament\Resources\AiModels\Schemas;

use App\Models\AiProvider;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AiModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ai_provider_id')
                    ->label('Provider')
                    ->options(fn (): array => AiProvider::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required(),
                TextInput::make('provider_model_id')
                    ->label('Model ID')
                    ->required()
                    ->default('mimo-v2.5-free')
                    ->maxLength(255),
                TextInput::make('name')
                    ->required()
                    ->default('mimo-v2.5-free')
                    ->maxLength(255),
                TextInput::make('endpoint_url')
                    ->label('Model endpoint override')
                    ->url()
                    ->default('https://opencode.ai/zen/v1/chat/completions')
                    ->maxLength(2048)
                    ->helperText('Leave empty to use the provider endpoint.')
                    ->columnSpanFull(),
                Toggle::make('supports_tool_calling')
                    ->label('Tool calling')
                    ->default(true),
                Toggle::make('supports_vision')
                    ->label('Vision')
                    ->default(true),
                Toggle::make('supports_streaming')
                    ->label('Streaming')
                    ->default(true),
                TextInput::make('max_input_tokens')
                    ->numeric()
                    ->minValue(1)
                    ->default(200000),
                TextInput::make('max_output_tokens')
                    ->numeric()
                    ->minValue(1)
                    ->default(32000),
                KeyValue::make('metadata')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
