<?php

namespace Tests\Feature;

use App\Enums\GmailAccountStatus;
use App\Models\GmailAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GmailTokenStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_tokens_are_encrypted_at_rest(): void
    {
        $account = GmailAccount::factory()->create([
            'access_token' => 'plain-access-token',
            'refresh_token' => 'plain-refresh-token',
        ]);

        $raw = DB::table('gmail_accounts')->where('id', $account->id)->first();

        $this->assertNotSame('plain-access-token', $raw->access_token);
        $this->assertNotSame('plain-refresh-token', $raw->refresh_token);
        $this->assertSame('plain-access-token', $account->fresh()->access_token);
        $this->assertSame('plain-refresh-token', $account->fresh()->refresh_token);
    }

    public function test_existing_refresh_token_is_preserved_when_google_returns_no_new_refresh_token(): void
    {
        $account = GmailAccount::factory()->create([
            'user_id' => User::factory(),
            'refresh_token' => 'existing-refresh-token',
        ]);

        $account->fill([
            'access_token' => 'new-access-token',
            'refresh_token' => null ?? $account->refresh_token,
            'sync_status' => GmailAccountStatus::Connected,
        ])->save();

        $this->assertSame('existing-refresh-token', $account->fresh()->refresh_token);
        $this->assertSame('new-access-token', $account->fresh()->access_token);
    }
}
