<?php

namespace App\Filament\Resources\AiFeatureSettings;

use App\Filament\Resources\AiFeatureSettings\Pages\CreateAiFeatureSetting;
use App\Filament\Resources\AiFeatureSettings\Pages\EditAiFeatureSetting;
use App\Filament\Resources\AiFeatureSettings\Pages\ListAiFeatureSettings;
use App\Filament\Resources\AiFeatureSettings\Schemas\AiFeatureSettingForm;
use App\Filament\Resources\AiFeatureSettings\Tables\AiFeatureSettingsTable;
use App\Models\AiFeatureSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AiFeatureSettingResource extends Resource
{
    protected static ?string $model = AiFeatureSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'AI Feature Settings';

    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return AiFeatureSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AiFeatureSettingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAiFeatureSettings::route('/'),
            'create' => CreateAiFeatureSetting::route('/create'),
            'edit' => EditAiFeatureSetting::route('/{record}/edit'),
        ];
    }
}
