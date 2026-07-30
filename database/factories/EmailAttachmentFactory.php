<?php

namespace Database\Factories;

use App\Models\EmailAttachment;
use App\Models\EmailMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailAttachment>
 */
class EmailAttachmentFactory extends Factory
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
            'gmail_attachment_id' => $gmailAttachmentId = fake()->unique()->uuid(),
            'gmail_attachment_key' => hash('sha256', $gmailAttachmentId),
            'filename' => fake()->word().'.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => fake()->numberBetween(1000, 100000),
            'is_downloaded' => false,
        ];
    }
}
