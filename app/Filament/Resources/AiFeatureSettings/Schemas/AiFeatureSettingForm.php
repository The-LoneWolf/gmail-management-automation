<?php

namespace App\Filament\Resources\AiFeatureSettings\Schemas;

use App\Enums\AiFeature;
use App\Models\AiModel;
use App\Models\AiProvider;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AiFeatureSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('feature')
                    ->options(AiFeature::options())
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Choose which product feature will use this provider/model selection.'),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('ai_provider_id')
                    ->label('Provider')
                    ->options(fn (): array => AiProvider::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required()
                    ->helperText('Create providers from AI Providers first.'),
                Select::make('ai_model_id')
                    ->label('Model')
                    ->options(fn (): array => AiModel::query()
                        ->with('provider')
                        ->orderBy('provider_model_id')
                        ->get()
                        ->mapWithKeys(fn (AiModel $model): array => [
                            $model->id => "{$model->provider_model_id} ({$model->provider->name})",
                        ])
                        ->all())
                    ->searchable()
                    ->required()
                    ->helperText('The selected model must belong to the selected provider. The resolver enforces this before any request is sent.'),
                TextInput::make('temperature')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(2)
                    ->step(0.01)
                    ->default(0.20),
                TextInput::make('max_output_tokens')
                    ->numeric()
                    ->minValue(1)
                    ->helperText('Optional feature-level override. Empty uses the model output limit.'),
                Textarea::make('system_prompt')
                    ->rows(4)
                    ->columnSpanFull(),
                KeyValue::make('request_overrides')
                    ->helperText('Optional non-secret request body values merged before per-call overrides.')
                    ->columnSpanFull(),
                Toggle::make('requires_json')
                    ->default(false),
                Toggle::make('requires_tools')
                    ->default(false),
                Toggle::make('requires_vision')
                    ->default(false),
                Toggle::make('is_enabled')
                    ->default(true),
            ]);
    }
}
