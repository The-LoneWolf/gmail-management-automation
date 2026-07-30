<?php

namespace App\Models;

use App\Enums\ReplyDraftStatus;
use Database\Factories\ReplyDraftFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'email_message_id',
    'email_thread_id',
    'user_id',
    'to_email',
    'subject',
    'body',
    'metadata',
    'status',
    'approved_by',
    'approved_at',
    'sent_at',
    'gmail_sent_message_id',
    'error_message',
])]
class ReplyDraft extends Model
{
    /** @use HasFactory<ReplyDraftFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'status' => ReplyDraftStatus::class,
            'approved_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(EmailMessage::class, 'email_message_id');
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(EmailThread::class, 'email_thread_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
