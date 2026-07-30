<?php

namespace App\Jobs;

use App\Enums\GmailAccountStatus;
use App\Models\GmailAccount;
use App\Services\Gmail\GmailHistorySyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SyncGmailHistory implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $gmailAccountId) {}

    public function handle(GmailHistorySyncService $historySync): void
    {
        $account = GmailAccount::findOrFail($this->gmailAccountId);

        try {
            $historySync->sync($account);

            $account->update([
                'sync_status' => GmailAccountStatus::Connected,
                'sync_error' => null,
            ]);
        } catch (Throwable $throwable) {
            $account->update([
                'sync_status' => GmailAccountStatus::Failed,
                'sync_error' => $throwable->getMessage(),
            ]);

            throw $throwable;
        }
    }
}
