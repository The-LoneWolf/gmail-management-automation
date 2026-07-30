<?php

namespace Tests\Feature;

use App\Enums\AutomationExecutionStatus;
use App\Models\AutomationExecution;
use App\Models\AutomationRule;
use App\Models\EmailMessage;
use App\Models\State;
use App\Services\Automation\AutomationRuleEvaluator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutomationRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_safe_rule_action_executes_and_updates_thread_state(): void
    {
        $message = EmailMessage::factory()->create();
        $state = State::factory()->create(['user_id' => $message->gmailAccount->user_id]);
        $message->thread()->update(['requires_reply' => true]);

        AutomationRule::factory()->create([
            'user_id' => $message->gmailAccount->user_id,
            'conditions' => ['all' => [['field' => 'requires_reply', 'operator' => 'equals', 'value' => true]]],
            'actions' => [['type' => 'set_state', 'state_id' => $state->id]],
        ]);

        app(AutomationRuleEvaluator::class)->evaluate($message->load('gmailAccount', 'thread', 'topics'), 'email.classified');

        $this->assertSame($state->id, $message->thread->fresh()->current_state_id);
        $this->assertSame(AutomationExecutionStatus::Executed, AutomationExecution::first()->status);
    }

    public function test_restricted_rule_action_requires_approval(): void
    {
        $message = EmailMessage::factory()->create();
        $message->thread()->update(['requires_reply' => true]);

        AutomationRule::factory()->create([
            'user_id' => $message->gmailAccount->user_id,
            'conditions' => ['all' => [['field' => 'requires_reply', 'operator' => 'equals', 'value' => true]]],
            'actions' => [['type' => 'send_email']],
        ]);

        app(AutomationRuleEvaluator::class)->evaluate($message->load('gmailAccount', 'thread', 'topics'), 'email.classified');

        $execution = AutomationExecution::first();
        $this->assertSame(AutomationExecutionStatus::RequiresApproval, $execution->status);
        $this->assertTrue($execution->requires_approval);
    }
}
