<?php

namespace App\Services\Google;

use App\Enums\GmailAccountStatus;
use App\Models\GmailAccount;
use Carbon\CarbonImmutable;
use Google\Client;
use Google\Service\Gmail;

class GoogleClientFactory
{
    public function make(): Client
    {
        $client = new Client;
        $client->setClientId((string) config('services.google.client_id'));
        $client->setClientSecret((string) config('services.google.client_secret'));
        $client->setRedirectUri((string) config('services.google.redirect_uri'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setIncludeGrantedScopes(true);
        $client->setScopes(config('services.google.gmail_scopes', []));

        return $client;
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
}
