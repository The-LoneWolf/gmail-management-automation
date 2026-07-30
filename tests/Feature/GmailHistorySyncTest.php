<?php

namespace Tests\Feature;

use App\Jobs\InitialGmailSync;
use App\Jobs\SyncGmailAccount;
use App\Jobs\SyncGmailHistory;
use App\Models\GmailAccount;
use App\Models\User;
use App\Services\Gmail\QueueGmailAccountSyncs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GmailHistorySyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_account_queues_initial_sync_when_history_id_is_missing(): void
    {
        Queue::fake();

        $account = GmailAccount::factory()->create(['history_id' => null]);

        (new SyncGmailAccount($account->id))->handle();

        Queue::assertPushed(InitialGmailSync::class, fn (InitialGmailSync $job): bool => $job->gmailAccountId === $account->id);
        Queue::assertNotPushed(SyncGmailHistory::class);
    }

    public function test_sync_account_queues_history_sync_when_history_id_exists(): void
    {
        Queue::fake();

        $account = GmailAccount::factory()->create(['history_id' => '10001']);

        (new SyncGmailAccount($account->id))->handle();

        Queue::assertPushed(SyncGmailHistory::class, fn (SyncGmailHistory $job): bool => $job->gmailAccountId === $account->id);
    }

    public function test_scheduler_command_queues_connected_accounts(): void
    {
        Queue::fake();

        GmailAccount::factory()->count(2)->create();
        GmailAccount::factory()->create(['sync_status' => 'disabled']);

        Artisan::call('gmail:sync-accounts');

        Queue::assertPushed(SyncGmailAccount::class, 2);
    }

    public function test_queue_syncs_can_be_limited_to_one_user(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        GmailAccount::factory()->create(['user_id' => $user->id]);
        GmailAccount::factory()->create(['user_id' => $otherUser->id]);
        GmailAccount::factory()->create(['user_id' => $user->id, 'sync_status' => 'disabled']);

        $count = app(QueueGmailAccountSyncs::class)->queue(userId: $user->id);

        $this->assertSame(1, $count);
        Queue::assertPushed(SyncGmailAccount::class, 1);
    }
}
