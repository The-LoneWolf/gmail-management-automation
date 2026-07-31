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

        $linkStyle = 'color:#1d4ed8;font-weight:700;text-decoration:none;';
        $stepStyle = 'border:1px solid #e5e7eb;border-radius:10px;padding:18px 20px;background:#ffffff;box-shadow:0 1px 2px rgba(15,23,42,.05);';
        $titleStyle = 'margin:0 0 10px;font-size:15px;font-weight:800;color:#111827;';
        $listStyle = 'margin:0;padding-left:20px;color:#374151;';
        $codeStyle = 'margin-top:12px;border-radius:8px;background:#111827;color:#f9fafb;padding:12px 14px;font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;font-size:12px;line-height:1.7;overflow:auto;';

        return new HtmlString(<<<HTML
            <div style="font-size:14px;line-height:1.65;color:#374151;">
                <div style="margin-bottom:18px;border:1px solid #facc15;border-radius:10px;background:#fefce8;padding:14px 16px;color:#713f12;">
                    <strong>Before you start:</strong> keep this tab open. You will configure Google Cloud in a new tab, then copy the Client ID and Client secret back into this form.
                </div>

                <div style="display:grid;gap:14px;">
                    <section style="{$stepStyle}">
                        <h3 style="{$titleStyle}">1. Create or select a Google Cloud project</h3>
                        <ol style="{$listStyle}">
                            <li>Open <a href="https://console.cloud.google.com/" target="_blank" rel="noopener noreferrer" style="{$linkStyle}">Google Cloud Console</a>.</li>
                            <li>Select your project from the top project switcher, or create a new project for this app.</li>
                        </ol>
                    </section>

                    <section style="{$stepStyle}">
                        <h3 style="{$titleStyle}">2. Enable Gmail API</h3>
                        <ol style="{$listStyle}">
                            <li>Open <a href="https://console.cloud.google.com/apis/library/gmail.googleapis.com" target="_blank" rel="noopener noreferrer" style="{$linkStyle}">Gmail API in the API Library</a>.</li>
                            <li>Click <strong>Enable</strong> for the selected project.</li>
                        </ol>
                    </section>

                    <section style="{$stepStyle}">
                        <h3 style="{$titleStyle}">3. Configure Google Auth Platform</h3>
                        <ol style="{$listStyle}">
                            <li>Open <a href="https://console.cloud.google.com/auth/overview" target="_blank" rel="noopener noreferrer" style="{$linkStyle}">Google Auth Platform</a>.</li>
                            <li>In <strong>Branding</strong>, set the app name, support email, and developer contact email.</li>
                            <li>In <strong>Audience</strong>, keep publishing status as <strong>Testing</strong> while developing.</li>
                            <li>Add your Gmail address under <strong>Test users</strong>. Only test users can connect while the app is in Testing mode.</li>
                        </ol>
                    </section>

                    <section style="{$stepStyle}">
                        <h3 style="{$titleStyle}">4. Add OAuth scopes</h3>
                        <ol style="{$listStyle}">
                            <li>Open <a href="https://console.cloud.google.com/auth/scopes" target="_blank" rel="noopener noreferrer" style="{$linkStyle}">Data Access / Scopes</a>.</li>
                            <li>Add these exact scopes:</li>
                        </ol>
                        <div style="{$codeStyle}">openid<br>email<br>profile<br>https://www.googleapis.com/auth/gmail.modify</div>
                        <p style="margin:12px 0 0;color:#4b5563;">The Gmail modify scope lets the app read Gmail messages and observe labels such as read, unread, starred, inbox, and archived.</p>
                    </section>

                    <section style="{$stepStyle}">
                        <h3 style="{$titleStyle}">5. Create the OAuth client</h3>
                        <ol style="{$listStyle}">
                            <li>Open <a href="https://console.cloud.google.com/auth/clients" target="_blank" rel="noopener noreferrer" style="{$linkStyle}">OAuth Clients</a>.</li>
                            <li>Click <strong>Create client</strong>.</li>
                            <li>Choose <strong>Web application</strong>.</li>
                            <li>For local development, add this authorized redirect URI:</li>
                        </ol>
                        <div style="{$codeStyle}">{$redirectUri}</div>
                        <p style="margin:12px 0 0;color:#4b5563;">If you deploy this app later, add the production callback URL too, for example <code style="background:#f3f4f6;border-radius:4px;padding:1px 5px;color:#111827;">https://your-domain.com/gmail/oauth/callback</code>.</p>
                    </section>

                    <section style="{$stepStyle}">
                        <h3 style="{$titleStyle}">6. Save credentials in this app</h3>
                        <ol style="{$listStyle}">
                            <li>Copy the Google <strong>Client ID</strong> into the Client ID field.</li>
                            <li>Copy the Google <strong>Client secret</strong> into the Client secret field.</li>
                            <li>Keep the redirect URI and scopes in this form exactly aligned with Google Cloud.</li>
                            <li>Save this OAuth configuration, then go to <strong>Gmail Accounts</strong> and click <strong>Connect Gmail</strong>.</li>
                        </ol>
                    </section>
                </div>
            </div>
        HTML);
    }
}
