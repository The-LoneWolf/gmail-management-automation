<?php

namespace App\Jobs;

use App\Enums\ReplyDraftStatus;
use App\Models\ReplyDraft;
use App\Services\Replies\GmailReplySender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SendApprovedReply implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $replyDraftId) {}

    public function handle(GmailReplySender $sender): void
    {
        $draft = ReplyDraft::query()->with(['message.gmailAccount', 'thread'])->findOrFail($this->replyDraftId);

        try {
            $sender->send($draft);
        } catch (Throwable $throwable) {
            $draft->update([
                'status' => ReplyDraftStatus::Failed,
                'error_message' => $throwable->getMessage(),
            ]);

            throw $throwable;
        }
    }
}
