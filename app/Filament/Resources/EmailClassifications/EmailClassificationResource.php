<?php

namespace App\Filament\Resources\EmailClassifications;

use App\Filament\Resources\EmailClassifications\Pages\EditEmailClassification;
use App\Filament\Resources\EmailClassifications\Pages\ListEmailClassifications;
use App\Filament\Resources\EmailClassifications\Schemas\EmailClassificationForm;
use App\Filament\Resources\EmailClassifications\Tables\EmailClassificationsTable;
use App\Models\EmailClassification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EmailClassificationResource extends Resource
{
    protected static ?string $model = EmailClassification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Classification';

    public static function form(Schema $schema): Schema
    {
        return EmailClassificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailClassificationsTable::configure($table);
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
            'index' => ListEmailClassifications::route('/'),
            'edit' => EditEmailClassification::route('/{record}/edit'),
        ];
    }
}
