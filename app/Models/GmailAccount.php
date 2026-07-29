<?php

namespace App\Models;

use App\Enums\GmailAccountStatus;
use Database\Factories\GmailAccountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'google_email',
    'access_token',
    'refresh_token',
    'token_expires_at',
    'history_id',
    'watch_expires_at',
    'last_synced_at',
    'sync_status',
    'sync_error',
])]
class GmailAccount extends Model
{
    /** @use HasFactory<GmailAccountFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'token_expires_at' => 'datetime',
            'watch_expires_at' => 'datetime',
            'last_synced_at' => 'datetime',
            'sync_status' => GmailAccountStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function threads(): HasMany
    {
        return $this->hasMany(EmailThread::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(EmailMessage::class);
    }
}
