<?php

namespace Tests\Unit;

use App\Enums\EmailDirection;
use App\Services\Gmail\GmailMessageParser;
use Google\Service\Gmail\Message;
use Google\Service\Gmail\MessagePart;
use Google\Service\Gmail\MessagePartBody;
use Google\Service\Gmail\MessagePartHeader;
use PHPUnit\Framework\TestCase;

class GmailMessageParserTest extends TestCase
{
    public function test_it_parses_headers_bodies_labels_and_attachments(): void
    {
        $message = new Message([
            'id' => 'msg-1',
            'threadId' => 'thread-1',
            'historyId' => '12345',
            'snippet' => 'Hello snippet',
            'internalDate' => '1767225600000',
            'labelIds' => ['INBOX', 'UNREAD', 'STARRED'],
            'payload' => new MessagePart([
                'mimeType' => 'multipart/mixed',
                'headers' => [
                    new MessagePartHeader(['name' => 'From', 'value' => 'Jane Doe <jane@example.com>']),
                    new MessagePartHeader(['name' => 'To', 'value' => 'Ops <ops@example.com>']),
                    new MessagePartHeader(['name' => 'Subject', 'value' => 'January invoice']),
                    new MessagePartHeader(['name' => 'Message-ID', 'value' => '<abc@example.com>']),
                    new MessagePartHeader(['name' => 'Date', 'value' => 'Thu, 01 Jan 2026 00:00:00 +0000']),
                ],
                'parts' => [
                    new MessagePart([
                        'mimeType' => 'text/plain',
                        'body' => new MessagePartBody(['data' => $this->base64Url('Plain body')]),
                    ]),
                    new MessagePart([
                        'mimeType' => 'text/html',
                        'body' => new MessagePartBody(['data' => $this->base64Url('<p onclick="x()">Hi</p><script>alert(1)</script>')]),
                    ]),
                    new MessagePart([
                        'filename' => 'invoice.pdf',
                        'mimeType' => 'application/pdf',
                        'body' => new MessagePartBody(['attachmentId' => 'att-1', 'size' => 2048]),
                    ]),
                ],
            ]),
        ]);

        $parsed = (new GmailMessageParser)->parse($message);

        $this->assertSame('msg-1', $parsed['gmail_message_id']);
        $this->assertSame('thread-1', $parsed['gmail_thread_id']);
        $this->assertSame('jane@example.com', $parsed['sender_email']);
        $this->assertSame('January invoice', $parsed['subject']);
        $this->assertSame('Plain body', $parsed['text_body']);
        $this->assertStringNotContainsString('<script>', $parsed['sanitized_html_body']);
        $this->assertStringNotContainsString('onclick', $parsed['sanitized_html_body']);
        $this->assertFalse($parsed['is_read']);
        $this->assertTrue($parsed['is_starred']);
        $this->assertFalse($parsed['is_archived']);
        $this->assertSame(EmailDirection::Incoming, $parsed['direction']);
        $this->assertSame('att-1', $parsed['attachments'][0]['gmail_attachment_id']);
    }

    private function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
