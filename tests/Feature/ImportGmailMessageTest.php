<?php

namespace Tests\Feature;

use App\Models\EmailAttachment;
use App\Models\EmailMessage;
use App\Models\EmailThread;
use App\Models\GmailAccount;
use App\Services\Gmail\GmailImportService;
use Google\Service\Gmail\Message;
use Google\Service\Gmail\MessagePart;
use Google\Service\Gmail\MessagePartBody;
use Google\Service\Gmail\MessagePartHeader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportGmailMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_is_idempotent_for_gmail_message_ids(): void
    {
        $account = GmailAccount::factory()->create();
        $service = app(GmailImportService::class);
        $message = $this->gmailMessage('Updated subject');

        $service->import($account, $message);
        $service->import($account, $message);

        $this->assertSame(1, EmailThread::count());
        $this->assertSame(1, EmailMessage::count());
        $this->assertSame(1, EmailAttachment::count());
        $this->assertSame('thread-1', EmailThread::first()->gmail_thread_id);
        $this->assertSame(1, EmailThread::first()->message_count);
        $this->assertSame('67890', $account->fresh()->history_id);
    }

    public function test_import_accepts_long_gmail_attachment_ids(): void
    {
        $account = GmailAccount::factory()->create();
        $service = app(GmailImportService::class);
        $attachmentId = str_repeat('ANGjdJ_', 80);

        $service->import($account, $this->gmailMessage('Long attachment ID', $attachmentId));

        $attachment = EmailAttachment::first();

        $this->assertNotNull($attachment);
        $this->assertSame($attachmentId, $attachment->gmail_attachment_id);
        $this->assertSame(hash('sha256', $attachmentId), $attachment->gmail_attachment_key);
    }

    private function gmailMessage(string $subject, string $attachmentId = 'att-1'): Message
    {
        return new Message([
            'id' => 'msg-1',
            'threadId' => 'thread-1',
            'historyId' => '67890',
            'snippet' => 'Snippet',
            'internalDate' => '1767225600000',
            'labelIds' => ['INBOX'],
            'payload' => new MessagePart([
                'mimeType' => 'multipart/mixed',
                'headers' => [
                    new MessagePartHeader(['name' => 'From', 'value' => 'Jane <jane@example.com>']),
                    new MessagePartHeader(['name' => 'To', 'value' => 'Me <me@example.com>']),
                    new MessagePartHeader(['name' => 'Subject', 'value' => $subject]),
                ],
                'parts' => [
                    new MessagePart([
                        'mimeType' => 'text/plain',
                        'body' => new MessagePartBody(['data' => rtrim(strtr(base64_encode('Body'), '+/', '-_'), '=')]),
                    ]),
                    new MessagePart([
                        'filename' => 'file.pdf',
                        'mimeType' => 'application/pdf',
                        'body' => new MessagePartBody(['attachmentId' => $attachmentId, 'size' => 99]),
                    ]),
                ],
            ]),
        ]);
    }
}
