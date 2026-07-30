<?php

namespace App\Console\Commands;

use App\Services\Gmail\QueueGmailAccountSyncs;
use Illuminate\Console\Command;

class SyncGmailAccounts extends Command
{
    protected $signature = 'gmail:sync-accounts {--account= : Sync one Gmail account ID}';

    protected $description = 'Queue Gmail synchronization for connected accounts.';

    public function handle(QueueGmailAccountSyncs $syncs): int
    {
        $count = $syncs->queue(accountId: $this->option('account') ? (int) $this->option('account') : null);

        $this->info("Queued {$count} Gmail account sync job(s).");

        return self::SUCCESS;
    }
}
