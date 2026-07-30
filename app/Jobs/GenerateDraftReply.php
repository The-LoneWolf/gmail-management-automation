<?php

namespace App\Jobs;

use App\Models\EmailMessage;
use App\Services\Replies\ReplyDraftService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateDraftReply implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $emailMessageId) {}

    public function handle(ReplyDraftService $drafts): void
    {
        $drafts->generate(EmailMessage::query()->with(['gmailAccount', 'thread'])->findOrFail($this->emailMessageId));
    }
}
