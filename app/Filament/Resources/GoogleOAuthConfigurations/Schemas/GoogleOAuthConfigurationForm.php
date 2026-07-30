<?php

namespace App\Filament\Resources\GoogleOAuthConfigurations\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class GoogleOAuthConfigurationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('google_cloud_redirect_uri')
                    ->label('Google Cloud authorized redirect URI')
                    ->content(fn (): HtmlString => new HtmlString('<code>'.e(url('/gmail/oauth/callback')).'</code>'))
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->required()
                    ->default('Default Google OAuth'),
                TextInput::make('client_id')
                    ->label('Client ID')
                    ->required()
                    ->placeholder('000000000000-example.apps.googleusercontent.com')
                    ->columnSpanFull(),
                TextInput::make('client_secret')
                    ->label('Client secret')
                    ->password()
                    ->revealable()
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('redirect_uri')
                    ->required()
                    ->default(fn (): string => url('/gmail/oauth/callback'))
                    ->columnSpanFull(),
                TagsInput::make('scopes')
                    ->separator(',')
                    ->default(['openid', 'email', 'profile', 'https://www.googleapis.com/auth/gmail.modify'])
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
