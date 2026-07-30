<?php

namespace Tests\Feature;

use App\Enums\ReplyDraftStatus;
use App\Models\EmailMessage;
use App\Services\Replies\GmailReplySender;
use App\Services\Replies\ReplyDraftService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyDraftTest extends TestCase
{
    use RefreshDatabase;

    public function test_reply_draft_generation_requires_approval_before_sending(): void
    {
        $message = EmailMessage::factory()->create([
            'sender_email' => 'customer@example.com',
            'subject' => 'Need help',
        ]);

        $draft = app(ReplyDraftService::class)->generate($message);

        $this->assertSame(ReplyDraftStatus::PendingApproval, $draft->status);
        $this->assertSame('customer@example.com', $draft->to_email);
        $this->assertStringStartsWith('Re:', $draft->subject);

        $this->expectException(\RuntimeException::class);
        app(GmailReplySender::class)->send($draft);
    }
}
