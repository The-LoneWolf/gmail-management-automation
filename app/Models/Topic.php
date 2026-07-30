<?php

namespace App\Models;

use Database\Factories\TopicFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'user_id',
    'name',
    'slug',
    'description',
    'examples',
    'keywords',
    'color',
    'minimum_confidence',
    'requires_human_review',
    'is_active',
])]
class Topic extends Model
{
    /** @use HasFactory<TopicFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'examples' => 'array',
            'keywords' => 'array',
            'minimum_confidence' => 'decimal:2',
            'requires_human_review' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): BelongsToMany
    {
        return $this->belongsToMany(EmailMessage::class, 'email_message_topic')
            ->withPivot(['confidence', 'matched_by', 'reasoning', 'confirmed_at', 'rejected_at'])
            ->withTimestamps();
    }
}
