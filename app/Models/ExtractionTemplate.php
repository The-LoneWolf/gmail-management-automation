<?php

namespace App\Models;

use Database\Factories\ExtractionTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'slug', 'description', 'schema', 'instructions', 'output_format', 'is_active'])]
class ExtractionTemplate extends Model
{
    /** @use HasFactory<ExtractionTemplateFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['schema' => 'array', 'is_active' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function extractions(): HasMany
    {
        return $this->hasMany(EmailExtraction::class);
    }
}
