<?php

namespace Tests\Feature;

use App\Models\GoogleOAuthConfiguration;
use App\Services\Google\GoogleClientFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GoogleOAuthConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_secret_is_encrypted_at_rest(): void
    {
        $configuration = GoogleOAuthConfiguration::factory()->create([
            'client_secret' => 'plain-client-secret',
        ]);

        $raw = DB::table('google_o_auth_configurations')->where('id', $configuration->id)->first();

        $this->assertNotSame('plain-client-secret', $raw->client_secret);
        $this->assertSame('plain-client-secret', $configuration->fresh()->client_secret);
    }

    public function test_google_client_factory_uses_active_database_configuration(): void
    {
        GoogleOAuthConfiguration::factory()->create([
            'client_id' => 'db-client-id.apps.googleusercontent.com',
            'client_secret' => 'db-secret',
            'redirect_uri' => 'http://localhost/gmail/oauth/callback',
            'scopes' => ['openid', 'email'],
            'is_active' => true,
        ]);

        $factory = app(GoogleClientFactory::class);
        $client = $factory->make();

        $this->assertTrue($factory->isConfigured());
        $this->assertStringContainsString('client_id=db-client-id.apps.googleusercontent.com', urldecode($client->createAuthUrl()));
    }
}
