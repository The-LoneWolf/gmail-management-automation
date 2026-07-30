<?php

namespace Database\Factories;

use App\Enums\ReplyDraftStatus;
use App\Models\EmailMessage;
use App\Models\EmailThread;
use App\Models\ReplyDraft;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReplyDraft>
 */
class ReplyDraftFactory extends Factory
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
            'email_thread_id' => EmailThread::factory(),
            'user_id' => User::factory(),
            'to_email' => fake()->safeEmail(),
            'subject' => 'Re: '.fake()->sentence(3),
            'body' => fake()->paragraph(),
            'metadata' => ['source' => 'factory'],
            'status' => ReplyDraftStatus::PendingApproval,
        ];
    }
}
