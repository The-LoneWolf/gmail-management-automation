<?php

namespace App\Services\Google;

use App\Enums\GmailAccountStatus;
use App\Models\GmailAccount;
use App\Models\GoogleOAuthConfiguration;
use Carbon\CarbonImmutable;
use Google\Client;
use Google\Service\Gmail;
use Illuminate\Support\Facades\Schema;

class GoogleClientFactory
{
    public function make(): Client
    {
        $credentials = $this->credentials();
        $client = new Client;
        $client->setClientId($credentials['client_id']);
        $client->setClientSecret($credentials['client_secret']);
        $client->setRedirectUri($credentials['redirect_uri']);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setIncludeGrantedScopes(true);
        $client->setScopes($credentials['scopes']);

        return $client;
    }

    public function isConfigured(): bool
    {
        $credentials = $this->credentials();

        return filled($credentials['client_id']) && filled($credentials['client_secret']);
    }

    public function forGmailAccount(GmailAccount $account): Client
    {
        $client = $this->make();
        $client->setAccessToken([
            'access_token' => $account->access_token,
            'refresh_token' => $account->refresh_token,
            'expires_in' => max(1, now()->diffInSeconds($account->token_expires_at, false)),
            'created' => now()->subSecond()->timestamp,
        ]);

        if ($client->isAccessTokenExpired()) {
            if (! $account->refresh_token) {
                $account->update([
                    'sync_status' => GmailAccountStatus::NeedsReconnect,
                    'sync_error' => 'Missing Gmail refresh token.',
                ]);

                return $client;
            }

            $token = $client->fetchAccessTokenWithRefreshToken($account->refresh_token);

            if (isset($token['error'])) {
                $account->update([
                    'sync_status' => GmailAccountStatus::NeedsReconnect,
                    'sync_error' => $token['error_description'] ?? $token['error'],
                ]);

                return $client;
            }

            $account->update([
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? $account->refresh_token,
                'token_expires_at' => CarbonImmutable::now()->addSeconds((int) ($token['expires_in'] ?? 3600)),
                'sync_status' => GmailAccountStatus::Connected,
                'sync_error' => null,
            ]);
        }

        return $client;
    }

    public function gmail(GmailAccount $account): Gmail
    {
        return new Gmail($this->forGmailAccount($account));
    }

    /**
     * @return array{client_id: string, client_secret: string, redirect_uri: string, scopes: array<int, string>}
     */
    private function credentials(): array
    {
        $configuration = Schema::hasTable('google_o_auth_configurations')
            ? GoogleOAuthConfiguration::query()->where('is_active', true)->latest()->first()
            : null;

        return [
            'client_id' => (string) ($configuration?->client_id ?: config('services.google.client_id')),
            'client_secret' => (string) ($configuration?->client_secret ?: config('services.google.client_secret')),
            'redirect_uri' => (string) ($configuration?->redirect_uri ?: config('services.google.redirect_uri')),
            'scopes' => $configuration?->scopes ?: config('services.google.gmail_scopes', []),
        ];
    }
}
