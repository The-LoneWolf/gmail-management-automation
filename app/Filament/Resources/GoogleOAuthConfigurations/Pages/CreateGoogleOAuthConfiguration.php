<?php

namespace App\Filament\Resources\GoogleOAuthConfigurations\Pages;

use App\Filament\Resources\GoogleOAuthConfigurations\GoogleOAuthConfigurationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\HtmlString;

class CreateGoogleOAuthConfiguration extends CreateRecord
{
    protected static string $resource = GoogleOAuthConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('google_oauth_setup_steps')
                ->label('Setup steps')
                ->icon('heroicon-o-question-mark-circle')
                ->modalHeading('Google OAuth setup steps')
                ->modalWidth('4xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalContent(fn (): HtmlString => $this->setupSteps()),
        ];
    }

    private function setupSteps(): HtmlString
    {
        $redirectUri = e(url('/gmail/oauth/callback'));

        return new HtmlString(<<<HTML
            <div class="space-y-6 text-sm leading-6 text-gray-700 dark:text-gray-200">
                <div class="rounded-lg border border-warning-300 bg-warning-50 p-4 text-warning-900 dark:border-warning-700 dark:bg-warning-950 dark:text-warning-100">
                    Keep this browser tab open while you configure Google Cloud. You will copy the Client ID and Client secret back into this form.
                </div>

                <section class="space-y-3">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">1. Create or select a Google Cloud project</h3>
                    <ol class="list-decimal space-y-2 pl-5">
                        <li>Open <a class="font-medium text-primary-600 underline" href="https://console.cloud.google.com/" target="_blank" rel="noopener noreferrer">Google Cloud Console</a>.</li>
                        <li>Select your project from the top project switcher, or create a new project for this app.</li>
                    </ol>
                </section>

                <section class="space-y-3">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">2. Enable Gmail API</h3>
                    <ol class="list-decimal space-y-2 pl-5">
                        <li>Open <a class="font-medium text-primary-600 underline" href="https://console.cloud.google.com/apis/library/gmail.googleapis.com" target="_blank" rel="noopener noreferrer">Gmail API in the API Library</a>.</li>
                        <li>Click <strong>Enable</strong> for the selected project.</li>
                    </ol>
                </section>

                <section class="space-y-3">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">3. Configure Google Auth Platform</h3>
                    <ol class="list-decimal space-y-2 pl-5">
                        <li>Open <a class="font-medium text-primary-600 underline" href="https://console.cloud.google.com/auth/overview" target="_blank" rel="noopener noreferrer">Google Auth Platform</a>.</li>
                        <li>In <strong>Branding</strong>, set the app name, support email, and developer contact email.</li>
                        <li>In <strong>Audience</strong>, keep publishing status as <strong>Testing</strong> while developing.</li>
                        <li>Add your Gmail address under <strong>Test users</strong>. Only test users can connect while the app is in Testing mode.</li>
                    </ol>
                </section>

                <section class="space-y-3">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">4. Add OAuth scopes</h3>
                    <ol class="list-decimal space-y-2 pl-5">
                        <li>Open <a class="font-medium text-primary-600 underline" href="https://console.cloud.google.com/auth/scopes" target="_blank" rel="noopener noreferrer">Data Access / Scopes</a>.</li>
                        <li>Add these scopes:</li>
                    </ol>
                    <div class="rounded-lg bg-gray-950 p-4 font-mono text-xs text-white">
                        openid<br>
                        email<br>
                        profile<br>
                        https://www.googleapis.com/auth/gmail.modify
                    </div>
                    <p>The Gmail modify scope lets the app read Gmail messages and observe labels such as read, unread, starred, inbox, and archived.</p>
                </section>

                <section class="space-y-3">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">5. Create the OAuth client</h3>
                    <ol class="list-decimal space-y-2 pl-5">
                        <li>Open <a class="font-medium text-primary-600 underline" href="https://console.cloud.google.com/auth/clients" target="_blank" rel="noopener noreferrer">Clients</a>.</li>
                        <li>Click <strong>Create client</strong>.</li>
                        <li>Choose <strong>Web application</strong>.</li>
                        <li>For local development, add this authorized redirect URI:</li>
                    </ol>
                    <div class="rounded-lg bg-gray-950 p-4 font-mono text-xs text-white">{$redirectUri}</div>
                    <p>If you deploy this app later, add the production callback URL too, for example <code>https://your-domain.com/gmail/oauth/callback</code>.</p>
                </section>

                <section class="space-y-3">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">6. Save credentials in this app</h3>
                    <ol class="list-decimal space-y-2 pl-5">
                        <li>Copy the Google <strong>Client ID</strong> into the Client ID field.</li>
                        <li>Copy the Google <strong>Client secret</strong> into the Client secret field.</li>
                        <li>Keep the redirect URI and scopes in this form exactly aligned with Google Cloud.</li>
                        <li>Save this OAuth configuration, then go to <strong>Gmail Accounts</strong> and click <strong>Connect Gmail</strong>.</li>
                    </ol>
                </section>
            </div>
        HTML);
    }
}
