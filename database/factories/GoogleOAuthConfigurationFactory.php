<?php

namespace Database\Factories;

use App\Models\GoogleOAuthConfiguration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GoogleOAuthConfiguration>
 */
class GoogleOAuthConfigurationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Default Google OAuth',
            'client_id' => fake()->uuid().'.apps.googleusercontent.com',
            'client_secret' => 'client-secret',
            'redirect_uri' => 'http://localhost/gmail/oauth/callback',
            'scopes' => ['openid', 'email', 'profile', 'https://www.googleapis.com/auth/gmail.modify'],
            'is_active' => true,
        ];
    }
}
