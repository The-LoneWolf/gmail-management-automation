<?php

namespace App\Http\Controllers;

use App\Enums\GmailAccountStatus;
use App\Jobs\InitialGmailSync;
use App\Models\GmailAccount;
use App\Services\Google\GoogleClientFactory;
use Carbon\CarbonImmutable;
use Google\Service\Oauth2;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GmailOAuthController extends Controller
{
    public function redirect(GoogleClientFactory $clients): RedirectResponse
    {
        abort_if(
            blank(config('services.google.client_id')) || blank(config('services.google.client_secret')),
            500,
            'Google OAuth is not configured. Set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in .env, then clear Laravel config cache.',
        );

        return redirect()->away($clients->make()->createAuthUrl());
    }

    public function callback(Request $request, GoogleClientFactory $clients): RedirectResponse
    {
        abort_unless($request->user(), 403);

        if ($request->filled('error')) {
            return redirect('/admin/gmail-accounts')->with('error', $request->string('error')->toString());
        }

        $client = $clients->make();
        $token = $client->fetchAccessTokenWithAuthCode((string) $request->query('code'));

        if (isset($token['error'])) {
            return redirect('/admin/gmail-accounts')->with('error', $token['error_description'] ?? $token['error']);
        }

        $client->setAccessToken($token);
        $profile = (new Oauth2($client))->userinfo->get();

        $account = GmailAccount::query()->firstOrNew([
            'user_id' => $request->user()->id,
            'google_email' => $profile->getEmail(),
        ]);

        $account->fill([
            'access_token' => $token['access_token'],
            'refresh_token' => $token['refresh_token'] ?? $account->refresh_token,
            'token_expires_at' => CarbonImmutable::now()->addSeconds((int) ($token['expires_in'] ?? 3600)),
            'sync_status' => GmailAccountStatus::Connected,
            'sync_error' => null,
        ])->save();

        InitialGmailSync::dispatch($account->id);

        return redirect('/admin/gmail-accounts')->with('status', 'Gmail account connected.');
    }
}
