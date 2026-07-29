<?php

namespace App\Models;

use Database\Factories\EmailAttachmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'email_message_id',
    'gmail_attachment_id',
    'filename',
    'mime_type',
    'size_bytes',
    'storage_disk',
    'storage_path',
    'content_hash',
    'extracted_text',
    'is_downloaded',
])]
class EmailAttachment extends Model
{
    /** @use HasFactory<EmailAttachmentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_downloaded' => 'boolean',
        ];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(EmailMessage::class, 'email_message_id');
    }
}
