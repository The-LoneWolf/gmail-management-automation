<?php

namespace App\Models;

use Database\Factories\AiProviderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'vendor',
    'api_type',
    'endpoint_url',
    'api_key',
    'secret_headers',
    'default_body',
    'timeout_seconds',
    'retry_attempts',
    'is_active',
])]
class AiProvider extends Model
{
    /** @use HasFactory<AiProviderFactory> */
    use HasFactory;

    protected $hidden = [
        'api_key',
        'secret_headers',
    ];

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'secret_headers' => 'encrypted:array',
            'default_body' => 'array',
            'timeout_seconds' => 'integer',
            'retry_attempts' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function models(): HasMany
    {
        return $this->hasMany(AiModel::class);
    }

    public function toProviderConfiguration(): array
    {
        return [
            'name' => $this->endpoint_url,
            'vendor' => $this->vendor,
            'apiType' => $this->api_type,
            'models' => $this->models->map->toProviderConfiguration()->values()->all(),
        ];
    }
}
