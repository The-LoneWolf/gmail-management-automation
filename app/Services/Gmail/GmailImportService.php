<?php

namespace App\Services\Gmail;

use App\Enums\EmailProcessingStatus;
use App\Jobs\ClassifyEmailWithAi;
use App\Models\EmailMessage;
use App\Models\EmailThread;
use App\Models\GmailAccount;
use Google\Service\Gmail\Message;
use Illuminate\Support\Arr;

class GmailImportService
{
    public function __construct(private readonly GmailMessageParser $parser) {}

    public function import(GmailAccount $account, Message $gmailMessage): EmailMessage
    {
        $parsed = $this->parser->parse($gmailMessage);
        $thread = $this->upsertThread($account, $parsed);

        $message = EmailMessage::updateOrCreate(
            [
                'gmail_account_id' => $account->id,
                'gmail_message_id' => $parsed['gmail_message_id'],
            ],
            [
                'email_thread_id' => $thread->id,
                'gmail_thread_id' => $parsed['gmail_thread_id'],
                'history_id' => $parsed['history_id'],
                'message_id_header' => $parsed['message_id_header'],
                'in_reply_to_header' => $parsed['in_reply_to_header'],
                'references_header' => $parsed['references_header'],
                'sender_name' => $parsed['sender_name'],
                'sender_email' => $parsed['sender_email'],
                'reply_to_email' => $parsed['reply_to_email'],
                'to_addresses' => $parsed['to_addresses'],
                'cc_addresses' => $parsed['cc_addresses'],
                'bcc_addresses' => $parsed['bcc_addresses'],
                'subject' => $parsed['subject'],
                'snippet' => $parsed['snippet'],
                'text_body' => $parsed['text_body'],
                'html_body' => $parsed['html_body'],
                'sanitized_html_body' => $parsed['sanitized_html_body'],
                'received_at' => $parsed['received_at'],
                'internal_date' => $parsed['internal_date'],
                'labels' => $parsed['labels'],
                'is_read' => $parsed['is_read'],
                'is_starred' => $parsed['is_starred'],
                'is_archived' => $parsed['is_archived'],
                'has_attachments' => $parsed['has_attachments'],
                'direction' => $parsed['direction'],
                'processing_status' => EmailProcessingStatus::Pending,
            ],
        );

        foreach ($parsed['attachments'] as $attachment) {
            $message->attachments()->updateOrCreate(
                ['gmail_attachment_key' => $this->attachmentKey($attachment['gmail_attachment_id'])],
                [
                    'gmail_attachment_id' => $attachment['gmail_attachment_id'],
                    ...Arr::only($attachment, ['filename', 'mime_type', 'size_bytes']),
                ],
            );
        }

        $this->refreshThreadCounters($thread);

        if ($parsed['history_id']) {
            $account->update(['history_id' => $parsed['history_id']]);
        }

        if ($message->wasRecentlyCreated || $message->ai_processed_at === null) {
            ClassifyEmailWithAi::dispatch($message->id);
        }

        return $message;
    }

    private function upsertThread(GmailAccount $account, array $parsed): EmailThread
    {
        return EmailThread::updateOrCreate(
            [
                'gmail_account_id' => $account->id,
                'gmail_thread_id' => $parsed['gmail_thread_id'],
            ],
            [
                'subject' => $parsed['subject'],
                'participants' => $this->participants($parsed),
                'last_message_at' => $parsed['received_at'],
            ],
        );
    }

    private function attachmentKey(string $gmailAttachmentId): string
    {
        return hash('sha256', $gmailAttachmentId);
    }

    private function refreshThreadCounters(EmailThread $thread): void
    {
        $thread->update([
            'message_count' => $thread->messages()->count(),
            'last_message_at' => $thread->messages()->max('received_at'),
        ]);
    }

    private function participants(array $parsed): array
    {
        return collect([
            ['name' => $parsed['sender_name'], 'email' => $parsed['sender_email']],
            ...$parsed['to_addresses'],
            ...($parsed['cc_addresses'] ?? []),
        ])
            ->filter(fn (array $participant): bool => filled($participant['email'] ?? null))
            ->unique('email')
            ->values()
            ->all();
    }
}
