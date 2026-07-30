<?php

namespace App\Models;

use App\Enums\ExtractionStatus;
use Database\Factories\EmailExtractionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'email_message_id',
    'extraction_template_id',
    'extracted_data',
    'confidence',
    'model',
    'prompt_version',
    'status',
    'error_message',
    'reviewed_by',
    'reviewed_at',
])]
class EmailExtraction extends Model
{
    /** @use HasFactory<EmailExtractionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'extracted_data' => 'array',
            'confidence' => 'decimal:2',
            'status' => ExtractionStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(EmailMessage::class, 'email_message_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ExtractionTemplate::class, 'extraction_template_id');
    }
}
