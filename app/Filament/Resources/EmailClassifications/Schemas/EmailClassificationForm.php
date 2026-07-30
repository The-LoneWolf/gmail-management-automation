<?php

namespace App\Filament\Resources\EmailClassifications\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmailClassificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('email_message_id')
                    ->relationship('message', 'subject')
                    ->disabled(),
                Textarea::make('summary')->disabled()->columnSpanFull(),
                Select::make('suggested_state_id')
                    ->relationship('suggestedState', 'name')
                    ->disabled(),
                TextInput::make('state_confidence')->disabled(),
                TextInput::make('priority')->disabled(),
                TextInput::make('sentiment')->disabled(),
                TextInput::make('language')->disabled(),
                Toggle::make('requires_reply')->disabled(),
                Toggle::make('requires_human_review')->disabled(),
                KeyValue::make('raw_result')->disabled()->columnSpanFull(),
                TextInput::make('model')->disabled(),
                TextInput::make('prompt_version')->disabled(),
                TextInput::make('status')->disabled(),
            ]);
    }
}
