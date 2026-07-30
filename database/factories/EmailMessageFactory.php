<?php

namespace Database\Factories;

use App\Enums\EmailDirection;
use App\Enums\EmailProcessingStatus;
use App\Models\EmailMessage;
use App\Models\EmailThread;
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
            'gmail_account_id' => fn (array $attributes): int => EmailThread::find($attributes['email_thread_id'])->gmail_account_id,
            'gmail_message_id' => fake()->unique()->uuid(),
            'gmail_thread_id' => fn (array $attributes): string => EmailThread::find($attributes['email_thread_id'])->gmail_thread_id,
            'message_id_header' => '<'.fake()->uuid().'@example.com>',
            'sender_name' => fake()->name(),
            'sender_email' => fake()->safeEmail(),
            'to_addresses' => [
                ['name' => 'Inbox Owner', 'email' => 'owner@example.com'],
            ],
            'cc_addresses' => [],
            'bcc_addresses' => [],
            'subject' => fake()->sentence(),
            'snippet' => fake()->sentence(12),
            'text_body' => fake()->paragraphs(2, true),
            'received_at' => now(),
            'internal_date' => now(),
            'labels' => ['INBOX'],
            'is_read' => false,
            'is_starred' => false,
            'is_archived' => false,
            'has_attachments' => false,
            'direction' => EmailDirection::Incoming,
            'processing_status' => EmailProcessingStatus::Pending,
        ];
    }
}
