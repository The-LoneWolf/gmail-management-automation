<?php

namespace Database\Factories;

use App\Enums\EmailDirection;
use App\Enums\EmailProcessingStatus;
use App\Models\EmailMessage;
use App\Models\EmailThread;
use App\Models\GmailAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailMessage>
 */
class EmailMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email_thread_id' => EmailThread::factory(),
            'gmail_account_id' => GmailAccount::factory(),
            'gmail_message_id' => fake()->unique()->uuid(),
            'gmail_thread_id' => fake()->unique()->uuid(),
            'sender_email' => fake()->safeEmail(),
            'to_addresses' => [],
            'subject' => fake()->sentence(),
            'received_at' => now(),
            'internal_date' => now(),
            'labels' => ['INBOX'],
            'direction' => EmailDirection::Incoming,
            'processing_status' => EmailProcessingStatus::Pending,
        ];
    }
}
