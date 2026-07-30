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
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertHeader('Content-Security-Policy', "default-src 'none'; script-src 'none'; style-src 'unsafe-inline' http: https:; img-src http: https: data: blob:; font-src http: https: data:; media-src http: https: data: blob:; connect-src 'none'; object-src 'none'; frame-src 'none'; base-uri 'none'; form-action 'none'; frame-ancestors 'self'")
            ->assertSee('<strong>there</strong>', false);
    }

    public function test_preview_allows_remote_email_images_and_fonts_but_blocks_scripts(): void
    {
        $message = EmailMessage::factory()->create([
            'sanitized_html_body' => '<img src="https://cdn.example.com/image.jpg" alt="Example"><script>alert(1)</script>',
        ]);

        $response = $this->actingAs($message->gmailAccount->user)
            ->get(route('email-messages.preview', $message))
            ->assertOk()
            ->assertSee('https://cdn.example.com/image.jpg', false);

        $policy = $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString('img-src http: https: data: blob:', $policy);
        $this->assertStringContainsString('font-src http: https: data:', $policy);
        $this->assertStringContainsString("script-src 'none'", $policy);
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
