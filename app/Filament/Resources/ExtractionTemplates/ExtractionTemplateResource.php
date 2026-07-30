<?php

namespace App\Filament\Resources\ExtractionTemplates;

use App\Filament\Resources\ExtractionTemplates\Pages\CreateExtractionTemplate;
use App\Filament\Resources\ExtractionTemplates\Pages\EditExtractionTemplate;
use App\Filament\Resources\ExtractionTemplates\Pages\ListExtractionTemplates;
use App\Filament\Resources\ExtractionTemplates\Schemas\ExtractionTemplateForm;
use App\Filament\Resources\ExtractionTemplates\Tables\ExtractionTemplatesTable;
use App\Models\ExtractionTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExtractionTemplateResource extends Resource
{
    protected static ?string $model = ExtractionTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ExtractionTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExtractionTemplatesTable::configure($table);
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
            'index' => ListExtractionTemplates::route('/'),
            'create' => CreateExtractionTemplate::route('/create'),
            'edit' => EditExtractionTemplate::route('/{record}/edit'),
        ];
    }
}
