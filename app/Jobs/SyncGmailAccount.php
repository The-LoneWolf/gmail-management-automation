<?php

namespace App\Jobs;

use App\Enums\GmailAccountStatus;
use App\Models\GmailAccount;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncGmailAccount implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $gmailAccountId) {}

    public function handle(): void
    {
        $account = GmailAccount::findOrFail($this->gmailAccountId);

        if (! $account->history_id) {
            InitialGmailSync::dispatch($account->id);

            return;
        }

        $account->update(['sync_status' => GmailAccountStatus::Syncing, 'sync_error' => null]);
        SyncGmailHistory::dispatch($account->id);
    }
}
