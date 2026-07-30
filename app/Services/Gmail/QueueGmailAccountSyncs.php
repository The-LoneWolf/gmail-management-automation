<?php

namespace App\Services\Gmail;

use App\Enums\GmailAccountStatus;
use App\Jobs\SyncGmailAccount;
use App\Models\GmailAccount;

class QueueGmailAccountSyncs
{
    public function queue(?int $userId = null, ?int $accountId = null): int
    {
        $query = GmailAccount::query()
            ->where('sync_status', GmailAccountStatus::Connected->value);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        if ($accountId !== null) {
            $query->whereKey($accountId);
        }

        $count = 0;

        $query->each(function (GmailAccount $account) use (&$count): void {
            SyncGmailAccount::dispatch($account->id);
            $count++;
        });

        return $count;
    }
}
