<?php

namespace Database\Factories;

use App\Enums\GmailAccountStatus;
use App\Models\GmailAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GmailAccount>
 */
class GmailAccountFactory extends Factory
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
            'google_email' => fake()->unique()->safeEmail(),
            'access_token' => 'access-token',
            'refresh_token' => 'refresh-token',
            'token_expires_at' => now()->addHour(),
            'sync_status' => GmailAccountStatus::Connected,
        ];
    }
}
