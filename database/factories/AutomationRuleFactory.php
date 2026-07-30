<?php

namespace Database\Factories;

use App\Models\AutomationRule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AutomationRule>
 */
class AutomationRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => 'High priority reply review',
            'trigger' => 'email.classified',
            'conditions' => [
                'all' => [
                    ['field' => 'requires_reply', 'operator' => 'equals', 'value' => true],
                ],
            ],
            'actions' => [
                ['type' => 'notify', 'channel_id' => null],
            ],
            'priority' => 100,
            'stop_processing' => false,
            'is_active' => true,
        ];
    }
}
