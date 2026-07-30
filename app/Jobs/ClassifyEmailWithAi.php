<?php

namespace App\Jobs;

use App\Enums\EmailProcessingStatus;
use App\Models\EmailMessage;
use App\Services\EmailIntelligence\EmailClassificationPersister;
use App\Services\EmailIntelligence\EmailIntelligenceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ClassifyEmailWithAi implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $emailMessageId) {}

    public function handle(EmailIntelligenceService $intelligence, EmailClassificationPersister $persister): void
    {
        $message = EmailMessage::query()
            ->with(['gmailAccount.user'])
            ->findOrFail($this->emailMessageId);

        $message->update(['processing_status' => EmailProcessingStatus::Classifying]);

        $user = $message->gmailAccount->user;
        $topics = $user->topics()->where('is_active', true)->get();
        $states = $user->states()->where('is_active', true)->orderBy('sort_order')->get();

        $result = $intelligence->classify($message, $topics, $states);

        $persister->persist($message, $result);
    }
}
