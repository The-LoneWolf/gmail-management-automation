<?php

namespace App\Filament\Resources\GmailAccounts;

use App\Filament\Resources\GmailAccounts\Pages\EditGmailAccount;
use App\Filament\Resources\GmailAccounts\Pages\ListGmailAccounts;
use App\Filament\Resources\GmailAccounts\Schemas\GmailAccountForm;
use App\Filament\Resources\GmailAccounts\Tables\GmailAccountsTable;
use App\Models\GmailAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GmailAccountResource extends Resource
{
    protected static ?string $model = GmailAccount::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAtSymbol;

    protected static ?string $navigationLabel = 'Gmail Accounts';

    protected static ?string $modelLabel = 'Gmail Account';

    public static function form(Schema $schema): Schema
    {
        return GmailAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GmailAccountsTable::configure($table);
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
            'index' => ListGmailAccounts::route('/'),
            'edit' => EditGmailAccount::route('/{record}/edit'),
        ];
    }
}
