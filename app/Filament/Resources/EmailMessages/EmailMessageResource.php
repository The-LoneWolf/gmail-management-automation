<?php

namespace App\Filament\Resources\EmailMessages;

use App\Filament\Resources\EmailMessages\Pages\EditEmailMessage;
use App\Filament\Resources\EmailMessages\Pages\ListEmailMessages;
use App\Filament\Resources\EmailMessages\Schemas\EmailMessageForm;
use App\Filament\Resources\EmailMessages\Tables\EmailMessagesTable;
use App\Models\EmailMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmailMessageResource extends Resource
{
    protected static ?string $model = EmailMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Inbox Messages';

    protected static ?string $modelLabel = 'Email Message';

    public static function form(Schema $schema): Schema
    {
        return EmailMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailMessagesTable::configure($table);
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
            'index' => ListEmailMessages::route('/'),
            'edit' => EditEmailMessage::route('/{record}/edit'),
        ];
    }
}
