<?php

namespace App\Filament\Resources\AiProviders\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AiProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->default('OpenCode Zen')
                    ->maxLength(255),
                Select::make('vendor')
                    ->required()
                    ->default('customendpoint')
                    ->options([
                        'customendpoint' => 'Custom endpoint',
                        'openai-compatible' => 'OpenAI compatible',
                        'openrouter' => 'OpenRouter',
                        'litellm' => 'LiteLLM proxy',
                    ]),
                Select::make('api_type')
                    ->label('API type')
                    ->required()
                    ->default('chat-completions')
                    ->options([
                        'chat-completions' => 'Chat completions',
                    ]),
                TextInput::make('endpoint_url')
                    ->label('Endpoint URL')
                    ->url()
                    ->required()
                    ->default('https://opencode.ai/zen/v1/chat/completions')
                    ->maxLength(2048)
                    ->columnSpanFull(),
                TextInput::make('api_key')
                    ->label('API key')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText('Optional for endpoints that do not require a key. Stored encrypted when provided.')
                    ->columnSpanFull(),
                KeyValue::make('secret_headers')
                    ->helperText('Optional encrypted headers for gateways that require custom auth or routing headers.')
                    ->columnSpanFull(),
                KeyValue::make('default_body')
                    ->helperText('Optional non-secret JSON defaults merged into future request bodies.')
                    ->columnSpanFull(),
                TextInput::make('timeout_seconds')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(300)
                    ->default(60),
                TextInput::make('retry_attempts')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(5)
                    ->default(2),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
