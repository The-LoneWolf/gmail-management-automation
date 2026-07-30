<?php

namespace App\Models;

use Database\Factories\EmailThreadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'gmail_account_id',
    'gmail_thread_id',
    'subject',
    'participants',
    'last_message_at',
    'message_count',
    'current_state_id',
    'assigned_user_id',
    'ai_summary',
    'priority',
    'requires_reply',
    'requires_human_review',
])]
class EmailThread extends Model
{
    /** @use HasFactory<EmailThreadFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'participants' => 'array',
            'last_message_at' => 'datetime',
            'requires_reply' => 'boolean',
            'requires_human_review' => 'boolean',
        ];
    }

    public function gmailAccount(): BelongsTo
    {
        return $this->belongsTo(GmailAccount::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function currentState(): BelongsTo
    {
        return $this->belongsTo(State::class, 'current_state_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(EmailMessage::class);
    }

    public function replyDrafts(): HasMany
    {
        return $this->hasMany(ReplyDraft::class);
    }
}
