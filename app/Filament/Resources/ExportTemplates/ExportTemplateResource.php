<?php

namespace App\Filament\Resources\ExportTemplates;

use App\Filament\Resources\ExportTemplates\Pages\CreateExportTemplate;
use App\Filament\Resources\ExportTemplates\Pages\EditExportTemplate;
use App\Filament\Resources\ExportTemplates\Pages\ListExportTemplates;
use App\Filament\Resources\ExportTemplates\Schemas\ExportTemplateForm;
use App\Filament\Resources\ExportTemplates\Tables\ExportTemplatesTable;
use App\Models\ExportTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExportTemplateResource extends Resource
{
    protected static ?string $model = ExportTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ExportTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExportTemplatesTable::configure($table);
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
            'index' => ListExportTemplates::route('/'),
            'create' => CreateExportTemplate::route('/create'),
            'edit' => EditExportTemplate::route('/{record}/edit'),
        ];
    }
}
