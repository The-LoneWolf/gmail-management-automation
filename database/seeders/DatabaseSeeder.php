<?php

namespace Database\Seeders;

use App\Enums\EmailDirection;
use App\Enums\EmailProcessingStatus;
use App\Models\AutomationRule;
use App\Models\EmailMessage;
use App\Models\EmailThread;
use App\Models\ExportTemplate;
use App\Models\ExtractionTemplate;
use App\Models\GmailAccount;
use App\Models\NotificationChannel;
use App\Models\State;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );

        $states = collect([
            ['name' => 'New', 'slug' => 'new', 'sort_order' => 10, 'color' => '#64748b', 'is_initial' => true],
            ['name' => 'Needs Review', 'slug' => 'needs-review', 'sort_order' => 20, 'color' => '#f59e0b'],
            ['name' => 'Action Required', 'slug' => 'action-required', 'sort_order' => 30, 'color' => '#dc2626'],
            ['name' => 'Waiting for Reply', 'slug' => 'waiting-for-reply', 'sort_order' => 40, 'color' => '#2563eb'],
            ['name' => 'Resolved', 'slug' => 'resolved', 'sort_order' => 90, 'color' => '#16a34a', 'is_final' => true],
        ])->map(fn (array $state): State => State::query()->updateOrCreate(
            ['user_id' => $user->id, 'slug' => $state['slug']],
            [
                'name' => $state['name'],
                'description' => $state['name'].' workflow state.',
                'type' => 'workflow',
                'sort_order' => $state['sort_order'],
                'color' => $state['color'],
                'is_initial' => $state['is_initial'] ?? false,
                'is_final' => $state['is_final'] ?? false,
                'is_active' => true,
            ],
        ));

        collect([
            ['name' => 'Invoice', 'slug' => 'invoice', 'keywords' => ['invoice', 'payment', 'billing'], 'color' => '#0f766e'],
            ['name' => 'Customer complaint', 'slug' => 'customer-complaint', 'keywords' => ['complaint', 'charged twice', 'refund'], 'color' => '#b91c1c', 'requires_human_review' => true],
            ['name' => 'Support request', 'slug' => 'support-request', 'keywords' => ['support', 'help', 'issue'], 'color' => '#7c3aed'],
            ['name' => 'Sales lead', 'slug' => 'sales-lead', 'keywords' => ['quote', 'pricing', 'demo'], 'color' => '#2563eb'],
        ])->each(fn (array $topic): Topic => Topic::query()->updateOrCreate(
            ['user_id' => $user->id, 'slug' => $topic['slug']],
            [
                'name' => $topic['name'],
                'description' => $topic['name'].' emails.',
                'examples' => ['Example '.$topic['name'].' email.'],
                'keywords' => $topic['keywords'],
                'color' => $topic['color'],
                'minimum_confidence' => 0.85,
                'requires_human_review' => $topic['requires_human_review'] ?? false,
                'is_active' => true,
            ],
        ));

        ExtractionTemplate::query()->updateOrCreate(
            ['user_id' => $user->id, 'slug' => 'invoice-extraction'],
            [
                'name' => 'Invoice extraction',
                'description' => 'Extract basic invoice fields from emails.',
                'schema' => [
                    'fields' => [
                        'invoice_number' => ['type' => 'string', 'pattern' => 'invoice\\s*#?\\s*([A-Z0-9-]+)'],
                        'amount' => ['type' => 'money'],
                        'sender_email' => ['type' => 'email'],
                    ],
                ],
                'instructions' => 'Capture invoice number, amount, and sender email.',
                'output_format' => 'json',
                'is_active' => true,
            ],
        );

        ExportTemplate::query()->updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Inbox CSV export'],
            [
                'format' => 'csv',
                'filters' => [],
                'columns' => [
                    ['label' => 'Received At', 'source' => 'email.received_at'],
                    ['label' => 'Sender', 'source' => 'email.sender_email'],
                    ['label' => 'Subject', 'source' => 'email.subject'],
                    ['label' => 'Priority', 'source' => 'thread.priority'],
                ],
                'is_active' => true,
            ],
        );

        $channel = NotificationChannel::query()->updateOrCreate(
            ['user_id' => $user->id, 'type' => 'database', 'name' => 'Database inbox'],
            ['configuration' => ['enabled' => true], 'is_active' => true],
        );

        $account = GmailAccount::query()->firstOrCreate(
            ['user_id' => $user->id, 'google_email' => 'connected@example.com'],
            [
                'access_token' => 'seed-access-token',
                'refresh_token' => 'seed-refresh-token',
                'token_expires_at' => now()->addHour(),
                'history_id' => '100001',
                'last_synced_at' => now(),
            ],
        );

        if ($account->messages()->doesntExist()) {
            $thread = EmailThread::factory()->create([
                'gmail_account_id' => $account->id,
                'gmail_thread_id' => 'seed-thread-1',
                'subject' => 'Charged twice for July invoice',
                'current_state_id' => $states->firstWhere('slug', 'new')?->id,
            ]);

            EmailMessage::factory()->create([
                'email_thread_id' => $thread->id,
                'gmail_account_id' => $account->id,
                'gmail_message_id' => 'seed-message-1',
                'gmail_thread_id' => $thread->gmail_thread_id,
                'sender_name' => 'Jane Customer',
                'sender_email' => 'jane@example.com',
                'subject' => 'Charged twice for July invoice',
                'snippet' => 'I was charged twice for my July invoice and need help.',
                'text_body' => 'Hello, I was charged twice for my July invoice. Can you please check and reply?',
                'direction' => EmailDirection::Incoming,
                'processing_status' => EmailProcessingStatus::Pending,
            ]);
        }

        AutomationRule::query()->updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Notify on replies needed'],
            [
                'description' => 'Create a safe notification when a message requires a reply.',
                'trigger' => 'email.classified',
                'conditions' => [
                    'all' => [
                        ['field' => 'requires_reply', 'operator' => 'equals', 'value' => true],
                    ],
                ],
                'actions' => [
                    ['type' => 'notify', 'channel_id' => $channel->id],
                ],
                'priority' => 100,
                'stop_processing' => false,
                'is_active' => true,
            ],
        );
    }
}
