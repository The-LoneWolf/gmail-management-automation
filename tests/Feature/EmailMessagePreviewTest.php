<?php

namespace Tests\Feature;

use App\Models\EmailMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailMessagePreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_preview_sanitized_html_body(): void
    {
        $message = EmailMessage::factory()->create([
            'sanitized_html_body' => '<p>Hello <strong>there</strong></p>',
        ]);

        $this->actingAs($message->gmailAccount->user)
            ->get(route('email-messages.preview', $message))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertSee('<strong>there</strong>', false);
    }

    public function test_preview_falls_back_to_plain_text_body(): void
    {
        $message = EmailMessage::factory()->create([
            'sanitized_html_body' => null,
            'text_body' => "First line\nSecond line",
        ]);

        $this->actingAs($message->gmailAccount->user)
            ->get(route('email-messages.preview', $message))
            ->assertOk()
            ->assertSee('First line<br>', false);
    }

    public function test_preview_is_limited_to_message_owner(): void
    {
        $message = EmailMessage::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('email-messages.preview', $message))
            ->assertForbidden();
    }
}
