<?php

namespace App\Jobs;

use App\Models\GmailAccount;
use App\Services\Gmail\GmailImportService;
use App\Services\Google\GoogleClientFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportGmailMessage implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $gmailAccountId, public string $gmailMessageId) {}

    public function handle(GoogleClientFactory $clients, GmailImportService $imports): void
    {
        $account = GmailAccount::findOrFail($this->gmailAccountId);
        $message = $clients->gmail($account)->users_messages->get('me', $this->gmailMessageId, [
            'format' => 'full',
        ]);

        $imports->import($account, $message);
    }
}
