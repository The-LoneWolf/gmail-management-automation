<?php

namespace App\Models;

use Database\Factories\AutomationRuleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'description', 'trigger', 'conditions', 'actions', 'priority', 'stop_processing', 'is_active', 'last_triggered_at'])]
class AutomationRule extends Model
{
    /** @use HasFactory<AutomationRuleFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'conditions' => 'array',
            'actions' => 'array',
            'stop_processing' => 'boolean',
            'is_active' => 'boolean',
            'last_triggered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function executions(): HasMany
    {
        return $this->hasMany(AutomationExecution::class);
    }
}
