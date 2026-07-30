<?php

namespace App\Models;

use App\Enums\AutomationExecutionStatus;
use Database\Factories\AutomationExecutionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'automation_rule_id',
    'email_message_id',
    'email_thread_id',
    'status',
    'matched_conditions',
    'executed_actions',
    'error_message',
    'requires_approval',
    'approved_by',
    'approved_at',
])]
class AutomationExecution extends Model
{
    /** @use HasFactory<AutomationExecutionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => AutomationExecutionStatus::class,
            'matched_conditions' => 'array',
            'executed_actions' => 'array',
            'requires_approval' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AutomationRule::class, 'automation_rule_id');
    }

    public function emailThread(): BelongsTo
    {
        return $this->belongsTo(EmailThread::class, 'email_thread_id');
    }

    public function emailMessage(): BelongsTo
    {
        return $this->belongsTo(EmailMessage::class, 'email_message_id');
    }
}
