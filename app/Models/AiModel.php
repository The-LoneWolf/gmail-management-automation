<?php

namespace App\Models;

use Database\Factories\AiModelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'ai_provider_id',
    'provider_model_id',
    'name',
    'endpoint_url',
    'supports_tool_calling',
    'supports_vision',
    'supports_streaming',
    'max_input_tokens',
    'max_output_tokens',
    'metadata',
    'is_active',
])]
class AiModel extends Model
{
    /** @use HasFactory<AiModelFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'supports_tool_calling' => 'boolean',
            'supports_vision' => 'boolean',
            'supports_streaming' => 'boolean',
            'max_input_tokens' => 'integer',
            'max_output_tokens' => 'integer',
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'ai_provider_id');
    }

    public function effectiveEndpointUrl(): string
    {
        return $this->endpoint_url ?: $this->provider->endpoint_url;
    }

    public function toProviderConfiguration(): array
    {
        return [
            'id' => $this->provider_model_id,
            'name' => $this->name,
            'url' => $this->effectiveEndpointUrl(),
            'toolCalling' => $this->supports_tool_calling,
            'vision' => $this->supports_vision,
            'maxInputTokens' => $this->max_input_tokens,
            'maxOutputTokens' => $this->max_output_tokens,
        ];
    }
}
