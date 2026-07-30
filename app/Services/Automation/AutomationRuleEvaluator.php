<?php

namespace App\Services\Automation;

use App\Enums\AutomationExecutionStatus;
use App\Models\AutomationExecution;
use App\Models\AutomationRule;
use App\Models\EmailMessage;

class AutomationRuleEvaluator
{
    public function __construct(private readonly AutomationActionExecutor $executor) {}

    public function evaluate(EmailMessage $message, string $trigger): int
    {
        $rules = AutomationRule::query()
            ->where('user_id', $message->gmailAccount->user_id)
            ->where('trigger', $trigger)
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();

        $matched = 0;

        foreach ($rules as $rule) {
            if (! $this->matches($message, $rule->conditions ?? [])) {
                continue;
            }

            $matched++;
            $execution = AutomationExecution::create([
                'automation_rule_id' => $rule->id,
                'email_message_id' => $message->id,
                'email_thread_id' => $message->email_thread_id,
                'status' => AutomationExecutionStatus::Matched,
                'matched_conditions' => $rule->conditions,
            ]);

            $this->executor->execute($execution, $rule->actions ?? []);
            $rule->update(['last_triggered_at' => now()]);

            if ($rule->stop_processing) {
                break;
            }
        }

        return $matched;
    }

    private function matches(EmailMessage $message, array $conditions): bool
    {
        foreach ($conditions['all'] ?? [] as $condition) {
            $field = $condition['field'] ?? '';
            $value = $condition['value'] ?? null;
            $actual = match ($field) {
                'priority' => $message->thread?->priority,
                'requires_reply' => $message->thread?->requires_reply,
                'sender_email' => $message->sender_email,
                'topic' => $message->topics->pluck('slug')->all(),
                default => data_get($message, $field),
            };

            if (! $this->compare($actual, $condition['operator'] ?? 'equals', $value)) {
                return false;
            }
        }

        return true;
    }

    private function compare(mixed $actual, string $operator, mixed $value): bool
    {
        return match ($operator) {
            'contains' => is_array($actual) ? in_array($value, $actual, true) : str_contains((string) $actual, (string) $value),
            '>=' => (float) $actual >= (float) $value,
            default => $actual == $value,
        };
    }
}
