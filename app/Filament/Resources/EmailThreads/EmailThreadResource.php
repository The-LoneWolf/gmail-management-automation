<?php

namespace App\Filament\Resources\EmailThreads;

use App\Filament\Resources\EmailThreads\Pages\EditEmailThread;
use App\Filament\Resources\EmailThreads\Pages\ListEmailThreads;
use App\Filament\Resources\EmailThreads\Schemas\EmailThreadForm;
use App\Filament\Resources\EmailThreads\Tables\EmailThreadsTable;
use App\Models\EmailThread;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmailThreadResource extends Resource
{
    protected static ?string $model = EmailThread::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static ?string $navigationLabel = 'Inbox Threads';

    protected static ?string $modelLabel = 'Email Thread';

    public static function form(Schema $schema): Schema
    {
        return EmailThreadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailThreadsTable::configure($table);
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
            'index' => ListEmailThreads::route('/'),
            'edit' => EditEmailThread::route('/{record}/edit'),
        ];
    }
}
