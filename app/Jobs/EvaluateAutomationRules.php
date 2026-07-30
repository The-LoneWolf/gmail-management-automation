<?php

namespace App\Jobs;

use App\Models\EmailMessage;
use App\Services\Automation\AutomationRuleEvaluator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EvaluateAutomationRules implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $emailMessageId, public string $trigger = 'email.classified') {}

    public function handle(AutomationRuleEvaluator $evaluator): void
    {
        $message = EmailMessage::query()->with(['gmailAccount', 'thread', 'topics'])->findOrFail($this->emailMessageId);
        $evaluator->evaluate($message, $this->trigger);
    }
}
