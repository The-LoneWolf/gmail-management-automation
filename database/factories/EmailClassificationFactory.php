<?php

namespace Database\Factories;

use App\Enums\ClassificationStatus;
use App\Enums\EmailPriority;
use App\Enums\EmailSentiment;
use App\Models\EmailClassification;
use App\Models\EmailMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailClassification>
 */
class EmailClassificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email_message_id' => EmailMessage::factory(),
            'summary' => fake()->sentence(12),
            'state_confidence' => 0.80,
            'priority' => EmailPriority::Normal,
            'sentiment' => EmailSentiment::Neutral,
            'language' => 'en',
            'requires_reply' => false,
            'requires_human_review' => false,
            'suggested_actions' => ['Review the message.'],
            'raw_result' => ['strategy' => 'factory'],
            'model' => 'local-keyword-v1',
            'prompt_version' => 'factory-v1',
            'status' => ClassificationStatus::Completed,
        ];
    }
}
