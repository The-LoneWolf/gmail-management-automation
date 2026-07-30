<?php

namespace App\Jobs;

use App\Models\EmailMessage;
use App\Services\Extraction\EmailExtractionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExtractEmailDataWithAi implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $emailMessageId) {}

    public function handle(EmailExtractionService $extractor): void
    {
        $message = EmailMessage::query()->with('gmailAccount.user.extractionTemplates')->findOrFail($this->emailMessageId);

        foreach ($message->gmailAccount->user->extractionTemplates()->where('is_active', true)->get() as $template) {
            $extractor->extract($message, $template);
        }
    }
}
