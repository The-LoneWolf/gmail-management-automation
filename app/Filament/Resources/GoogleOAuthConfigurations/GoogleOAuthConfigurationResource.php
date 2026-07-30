<?php

namespace App\Filament\Resources\GoogleOAuthConfigurations;

use App\Filament\Resources\GoogleOAuthConfigurations\Pages\CreateGoogleOAuthConfiguration;
use App\Filament\Resources\GoogleOAuthConfigurations\Pages\EditGoogleOAuthConfiguration;
use App\Filament\Resources\GoogleOAuthConfigurations\Pages\ListGoogleOAuthConfigurations;
use App\Filament\Resources\GoogleOAuthConfigurations\Schemas\GoogleOAuthConfigurationForm;
use App\Filament\Resources\GoogleOAuthConfigurations\Tables\GoogleOAuthConfigurationsTable;
use App\Models\GoogleOAuthConfiguration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GoogleOAuthConfigurationResource extends Resource
{
    protected static ?string $model = GoogleOAuthConfiguration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Google OAuth Setup';

    public static function form(Schema $schema): Schema
    {
        return GoogleOAuthConfigurationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GoogleOAuthConfigurationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGoogleOAuthConfigurations::route('/'),
            'create' => CreateGoogleOAuthConfiguration::route('/create'),
            'edit' => EditGoogleOAuthConfiguration::route('/{record}/edit'),
        ];
    }
}
