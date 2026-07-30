<?php

namespace App\Filament\Resources\AutomationExecutions;

use App\Filament\Resources\AutomationExecutions\Pages\CreateAutomationExecution;
use App\Filament\Resources\AutomationExecutions\Pages\EditAutomationExecution;
use App\Filament\Resources\AutomationExecutions\Pages\ListAutomationExecutions;
use App\Filament\Resources\AutomationExecutions\Schemas\AutomationExecutionForm;
use App\Filament\Resources\AutomationExecutions\Tables\AutomationExecutionsTable;
use App\Models\AutomationExecution;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AutomationExecutionResource extends Resource
{
    protected static ?string $model = AutomationExecution::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AutomationExecutionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AutomationExecutionsTable::configure($table);
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
            'index' => ListAutomationExecutions::route('/'),
            'create' => CreateAutomationExecution::route('/create'),
            'edit' => EditAutomationExecution::route('/{record}/edit'),
        ];
    }
}
