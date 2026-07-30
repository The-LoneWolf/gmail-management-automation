<?php

namespace App\Models;

use App\Enums\EmailDirection;
use App\Enums\EmailProcessingStatus;
use Database\Factories\EmailMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'email_thread_id',
    'gmail_account_id',
    'gmail_message_id',
    'gmail_thread_id',
    'history_id',
    'message_id_header',
    'in_reply_to_header',
    'references_header',
    'sender_name',
    'sender_email',
    'reply_to_email',
    'to_addresses',
    'cc_addresses',
    'bcc_addresses',
    'subject',
    'snippet',
    'text_body',
    'html_body',
    'sanitized_html_body',
    'received_at',
    'internal_date',
    'labels',
    'is_read',
    'is_starred',
    'is_archived',
    'has_attachments',
    'direction',
    'processing_status',
    'ai_processed_at',
])]
class EmailMessage extends Model
{
    /** @use HasFactory<EmailMessageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'to_addresses' => 'array',
            'cc_addresses' => 'array',
            'bcc_addresses' => 'array',
            'labels' => 'array',
            'received_at' => 'datetime',
            'internal_date' => 'datetime',
            'is_read' => 'boolean',
            'is_starred' => 'boolean',
            'is_archived' => 'boolean',
            'has_attachments' => 'boolean',
            'ai_processed_at' => 'datetime',
            'direction' => EmailDirection::class,
            'processing_status' => EmailProcessingStatus::class,
        ];
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(EmailThread::class, 'email_thread_id');
    }

    public function gmailAccount(): BelongsTo
    {
        return $this->belongsTo(GmailAccount::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(EmailAttachment::class);
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'email_message_topic')
            ->withPivot(['confidence', 'matched_by', 'reasoning', 'confirmed_at', 'rejected_at'])
            ->withTimestamps();
    }

    public function classifications(): HasMany
    {
        return $this->hasMany(EmailClassification::class);
    }

    public function extractions(): HasMany
    {
        return $this->hasMany(EmailExtraction::class);
    }

    public function replyDrafts(): HasMany
    {
        return $this->hasMany(ReplyDraft::class);
    }
}
