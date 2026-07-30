<?php

namespace Database\Factories;

use App\Enums\AutomationExecutionStatus;
use App\Models\AutomationExecution;
use App\Models\AutomationRule;
use App\Models\EmailMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AutomationExecution>
 */
class AutomationExecutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'automation_rule_id' => AutomationRule::factory(),
            'email_message_id' => EmailMessage::factory(),
            'status' => AutomationExecutionStatus::Executed,
            'matched_conditions' => ['all' => []],
            'executed_actions' => [],
            'requires_approval' => false,
        ];
    }
}
