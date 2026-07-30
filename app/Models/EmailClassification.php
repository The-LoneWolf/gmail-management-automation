<?php

namespace App\Models;

use App\Enums\ClassificationStatus;
use App\Enums\EmailPriority;
use App\Enums\EmailSentiment;
use Database\Factories\EmailClassificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'email_message_id',
    'summary',
    'suggested_state_id',
    'state_confidence',
    'priority',
    'sentiment',
    'language',
    'requires_reply',
    'requires_human_review',
    'suggested_actions',
    'raw_result',
    'model',
    'prompt_version',
    'status',
    'error_message',
])]
class EmailClassification extends Model
{
    /** @use HasFactory<EmailClassificationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'state_confidence' => 'decimal:2',
            'requires_reply' => 'boolean',
            'requires_human_review' => 'boolean',
            'suggested_actions' => 'array',
            'raw_result' => 'array',
            'priority' => EmailPriority::class,
            'sentiment' => EmailSentiment::class,
            'status' => ClassificationStatus::class,
        ];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(EmailMessage::class, 'email_message_id');
    }

    public function suggestedState(): BelongsTo
    {
        return $this->belongsTo(State::class, 'suggested_state_id');
    }
}
