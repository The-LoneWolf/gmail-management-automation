<?php

namespace App\Services\Replies;

use App\Enums\ReplyDraftStatus;
use App\Models\ReplyDraft;
use App\Services\Google\GoogleClientFactory;
use Google\Service\Gmail\Message;

class GmailReplySender
{
    public function __construct(private readonly GoogleClientFactory $clients) {}

    public function send(ReplyDraft $draft): ReplyDraft
    {
        if (! $draft->approved_at) {
            throw new \RuntimeException('Reply draft must be approved before sending.');
        }

        $draft->update(['status' => ReplyDraftStatus::Sending]);
        $message = new Message;
        $message->setThreadId($draft->thread->gmail_thread_id);
        $message->setRaw($this->rawMessage($draft));

        $sent = $this->clients
            ->gmail($draft->message->gmailAccount)
            ->users_messages
            ->send('me', $message);

        $draft->update([
            'status' => ReplyDraftStatus::Sent,
            'sent_at' => now(),
            'gmail_sent_message_id' => $sent->getId(),
        ]);

        return $draft;
    }

    private function rawMessage(ReplyDraft $draft): string
    {
        $headers = [
            'To: '.$draft->to_email,
            'Subject: '.$draft->subject,
            'In-Reply-To: '.$draft->message->message_id_header,
            'References: '.trim(($draft->message->references_header ?? '').' '.($draft->message->message_id_header ?? '')),
            'Content-Type: text/plain; charset=utf-8',
        ];

        return rtrim(strtr(base64_encode(implode("\r\n", $headers)."\r\n\r\n".$draft->body), '+/', '-_'), '=');
    }
}
