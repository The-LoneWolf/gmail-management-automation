<?php

namespace App\Filament\Resources\EmailExtractions;

use App\Filament\Resources\EmailExtractions\Pages\CreateEmailExtraction;
use App\Filament\Resources\EmailExtractions\Pages\EditEmailExtraction;
use App\Filament\Resources\EmailExtractions\Pages\ListEmailExtractions;
use App\Filament\Resources\EmailExtractions\Schemas\EmailExtractionForm;
use App\Filament\Resources\EmailExtractions\Tables\EmailExtractionsTable;
use App\Models\EmailExtraction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmailExtractionResource extends Resource
{
    protected static ?string $model = EmailExtraction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EmailExtractionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailExtractionsTable::configure($table);
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
            'index' => ListEmailExtractions::route('/'),
            'create' => CreateEmailExtraction::route('/create'),
            'edit' => EditEmailExtraction::route('/{record}/edit'),
        ];
    }
}
