<?php

namespace Tests\Feature;

use App\Enums\EmailProcessingStatus;
use App\Jobs\ClassifyEmailWithAi;
use App\Models\EmailClassification;
use App\Models\EmailMessage;
use App\Models\EmailThread;
use App\Models\GmailAccount;
use App\Models\State;
use App\Models\Topic;
use App\Models\User;
use App\Services\EmailIntelligence\EmailClassificationPersister;
use App\Services\EmailIntelligence\EmailIntelligenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailClassificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_keyword_classifier_persists_classification_topics_and_thread_state(): void
    {
        $user = User::factory()->create();
        $account = GmailAccount::factory()->create(['user_id' => $user->id]);
        $state = State::factory()->create([
            'user_id' => $user->id,
            'name' => 'Action Required',
            'slug' => 'action-required',
        ]);
        $topic = Topic::factory()->create([
            'user_id' => $user->id,
            'name' => 'Invoice',
            'slug' => 'invoice',
            'keywords' => ['invoice', 'charged twice'],
        ]);
        $thread = EmailThread::factory()->create(['gmail_account_id' => $account->id]);
        $message = EmailMessage::factory()->create([
            'email_thread_id' => $thread->id,
            'gmail_account_id' => $account->id,
            'gmail_thread_id' => $thread->gmail_thread_id,
            'subject' => 'Charged twice for invoice',
            'text_body' => 'I was charged twice for this invoice. Can you please reply?',
        ]);

        (new ClassifyEmailWithAi($message->id))->handle(
            app(EmailIntelligenceService::class),
            app(EmailClassificationPersister::class),
        );

        $this->assertSame(1, EmailClassification::count());
        $this->assertSame(EmailProcessingStatus::Completed, $message->fresh()->processing_status);
        $this->assertTrue($message->fresh()->topics->contains($topic));
        $this->assertSame($state->id, $thread->fresh()->current_state_id);
        $this->assertTrue($thread->fresh()->requires_reply);
        $this->assertSame('high', $thread->fresh()->priority);
    }
}
