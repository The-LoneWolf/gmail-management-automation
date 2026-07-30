<?php

namespace App\Models;

use Database\Factories\ExportTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'format', 'filters', 'columns', 'field_mapping', 'schedule', 'destination', 'is_active'])]
class ExportTemplate extends Model
{
    /** @use HasFactory<ExportTemplateFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'columns' => 'array',
            'field_mapping' => 'array',
            'schedule' => 'array',
            'destination' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exports(): HasMany
    {
        return $this->hasMany(GeneratedExport::class);
    }
}
