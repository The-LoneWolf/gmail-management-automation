<?php

namespace App\Filament\Resources\ReplyDrafts;

use App\Filament\Resources\ReplyDrafts\Pages\CreateReplyDraft;
use App\Filament\Resources\ReplyDrafts\Pages\EditReplyDraft;
use App\Filament\Resources\ReplyDrafts\Pages\ListReplyDrafts;
use App\Filament\Resources\ReplyDrafts\Schemas\ReplyDraftForm;
use App\Filament\Resources\ReplyDrafts\Tables\ReplyDraftsTable;
use App\Models\ReplyDraft;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReplyDraftResource extends Resource
{
    protected static ?string $model = ReplyDraft::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ReplyDraftForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReplyDraftsTable::configure($table);
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
            'index' => ListReplyDrafts::route('/'),
            'create' => CreateReplyDraft::route('/create'),
            'edit' => EditReplyDraft::route('/{record}/edit'),
        ];
    }
}
