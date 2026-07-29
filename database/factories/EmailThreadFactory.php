<?php

namespace Database\Factories;

use App\Models\EmailThread;
use App\Models\GmailAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailThread>
 */
class EmailThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gmail_account_id' => GmailAccount::factory(),
            'gmail_thread_id' => fake()->unique()->uuid(),
            'subject' => fake()->sentence(),
            'participants' => [],
            'last_message_at' => now(),
            'message_count' => 0,
        ];
    }
}
