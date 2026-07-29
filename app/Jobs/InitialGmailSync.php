<?php

namespace App\Jobs;

use App\Enums\GmailAccountStatus;
use App\Models\GmailAccount;
use App\Services\Google\GoogleClientFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class InitialGmailSync implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $gmailAccountId, public int $maxMessages = 100) {}

    public function handle(GoogleClientFactory $clients): void
    {
        $account = GmailAccount::findOrFail($this->gmailAccountId);
        $account->update(['sync_status' => GmailAccountStatus::Syncing, 'sync_error' => null]);

        try {
            $gmail = $clients->gmail($account);
            $pageToken = null;
            $queued = 0;

            do {
                $response = $gmail->users_messages->listUsersMessages('me', [
                    'maxResults' => min(50, $this->maxMessages - $queued),
                    'pageToken' => $pageToken,
                ]);

                foreach ($response->getMessages() ?? [] as $message) {
                    ImportGmailMessage::dispatch($account->id, (string) $message->getId());
                    $queued++;

                    if ($queued >= $this->maxMessages) {
                        break 2;
                    }
                }

                $pageToken = $response->getNextPageToken();
            } while ($pageToken);

            $account->update([
                'sync_status' => GmailAccountStatus::Connected,
                'last_synced_at' => now(),
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
