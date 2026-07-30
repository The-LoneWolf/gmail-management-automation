<?php

namespace App\Services\Gmail;

use App\Jobs\ImportGmailMessage;
use App\Models\GmailAccount;
use App\Services\Google\GoogleClientFactory;

class GmailHistorySyncService
{
    public function __construct(private readonly GoogleClientFactory $clients) {}

    public function sync(GmailAccount $account): int
    {
        if (! $account->history_id) {
            return 0;
        }

        $gmail = $this->clients->gmail($account);
        $pageToken = null;
        $messageIds = collect();
        $latestHistoryId = $account->history_id;

        do {
            $response = $gmail->users_history->listUsersHistory('me', [
                'startHistoryId' => $account->history_id,
                'historyTypes' => ['messageAdded', 'labelAdded', 'labelRemoved'],
                'pageToken' => $pageToken,
            ]);

            foreach ($response->getHistory() ?? [] as $history) {
                if ($history->getId()) {
                    $latestHistoryId = (string) $history->getId();
                }

                foreach ($history->getMessagesAdded() ?? [] as $added) {
                    if ($added->getMessage()?->getId()) {
                        $messageIds->push((string) $added->getMessage()->getId());
                    }
                }

                foreach ($history->getLabelsAdded() ?? [] as $labelAdded) {
                    if ($labelAdded->getMessage()?->getId()) {
                        $messageIds->push((string) $labelAdded->getMessage()->getId());
                    }
                }

                foreach ($history->getLabelsRemoved() ?? [] as $labelRemoved) {
                    if ($labelRemoved->getMessage()?->getId()) {
                        $messageIds->push((string) $labelRemoved->getMessage()->getId());
                    }
                }
            }

            $pageToken = $response->getNextPageToken();
        } while ($pageToken);

        $ids = $messageIds->unique()->values();

        foreach ($ids as $messageId) {
            ImportGmailMessage::dispatch($account->id, $messageId);
        }

        $account->update([
            'history_id' => $latestHistoryId,
            'last_synced_at' => now(),
        ]);

        return $ids->count();
    }
}
