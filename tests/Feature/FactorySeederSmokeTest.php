<?php

namespace Tests\Feature;

use App\Models\EmailMessage;
use App\Models\GmailAccount;
use App\Models\State;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactorySeederSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_a_usable_demo_dataset(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', ['email' => 'admin@example.com']);
        $this->assertSame(1, GmailAccount::count());
        $this->assertGreaterThanOrEqual(4, Topic::count());
        $this->assertGreaterThanOrEqual(5, State::count());
        $this->assertSame(1, EmailMessage::count());
    }

    public function test_factories_create_consistent_email_graphs(): void
    {
        $message = EmailMessage::factory()->create();

        $this->assertSame($message->thread->gmail_account_id, $message->gmail_account_id);
        $this->assertSame($message->thread->gmail_thread_id, $message->gmail_thread_id);
        $this->assertInstanceOf(User::class, $message->gmailAccount->user);
    }
}
