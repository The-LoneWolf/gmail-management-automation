<?php

namespace App\Services\Replies;

use App\Enums\ReplyDraftStatus;
use App\Models\EmailMessage;
use App\Models\ReplyDraft;

class ReplyDraftService
{
    public function generate(EmailMessage $message): ReplyDraft
    {
        return ReplyDraft::create([
            'email_message_id' => $message->id,
            'email_thread_id' => $message->email_thread_id,
            'user_id' => $message->gmailAccount->user_id,
            'to_email' => $message->reply_to_email ?: $message->sender_email,
            'subject' => str_starts_with((string) $message->subject, 'Re:') ? (string) $message->subject : 'Re: '.$message->subject,
            'body' => "Hello,\n\nThank you for your message. I will review this and follow up shortly.\n\nRegards,",
            'metadata' => ['source' => 'local-draft-v1'],
            'status' => ReplyDraftStatus::PendingApproval,
        ]);
    }
}
