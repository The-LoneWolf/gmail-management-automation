<?php

namespace App\Filament\Resources\GeneratedExports;

use App\Filament\Resources\GeneratedExports\Pages\CreateGeneratedExport;
use App\Filament\Resources\GeneratedExports\Pages\EditGeneratedExport;
use App\Filament\Resources\GeneratedExports\Pages\ListGeneratedExports;
use App\Filament\Resources\GeneratedExports\Schemas\GeneratedExportForm;
use App\Filament\Resources\GeneratedExports\Tables\GeneratedExportsTable;
use App\Models\GeneratedExport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GeneratedExportResource extends Resource
{
    protected static ?string $model = GeneratedExport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return GeneratedExportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GeneratedExportsTable::configure($table);
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
            'index' => ListGeneratedExports::route('/'),
            'create' => CreateGeneratedExport::route('/create'),
            'edit' => EditGeneratedExport::route('/{record}/edit'),
        ];
    }
}
