<?php

namespace App\Console\Commands;

use App\Enums\GmailAccountStatus;
use App\Jobs\SyncGmailAccount;
use App\Models\GmailAccount;
use Illuminate\Console\Command;

class SyncGmailAccounts extends Command
{
    protected $signature = 'gmail:sync-accounts {--account= : Sync one Gmail account ID}';

    protected $description = 'Queue Gmail synchronization for connected accounts.';

    public function handle(): int
    {
        $query = GmailAccount::query()
            ->where('sync_status', GmailAccountStatus::Connected->value);

        if ($this->option('account')) {
            $query->whereKey($this->option('account'));
        }

        $count = 0;

        $query->each(function (GmailAccount $account) use (&$count): void {
            SyncGmailAccount::dispatch($account->id);
            $count++;
        });

        $this->info("Queued {$count} Gmail account sync job(s).");

        return self::SUCCESS;
    }
}
