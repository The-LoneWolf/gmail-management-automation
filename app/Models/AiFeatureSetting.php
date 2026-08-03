<?php

namespace App\Models;

use App\Enums\AiFeature;
use Database\Factories\AiFeatureSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'feature',
    'name',
    'ai_provider_id',
    'ai_model_id',
    'temperature',
    'max_output_tokens',
    'system_prompt',
    'request_overrides',
    'requires_json',
    'requires_tools',
    'requires_vision',
    'is_enabled',
])]
class AiFeatureSetting extends Model
{
    /** @use HasFactory<AiFeatureSettingFactory> */
    use HasFactory;

    protected $attributes = [
        'temperature' => 0.20,
        'requires_json' => false,
        'requires_tools' => false,
        'requires_vision' => false,
        'is_enabled' => true,
    ];

    protected function casts(): array
    {
        return [
            'feature' => AiFeature::class,
            'temperature' => 'decimal:2',
            'max_output_tokens' => 'integer',
            'request_overrides' => 'array',
            'requires_json' => 'boolean',
            'requires_tools' => 'boolean',
            'requires_vision' => 'boolean',
            'is_enabled' => 'boolean',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'ai_provider_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'ai_model_id');
    }
}
