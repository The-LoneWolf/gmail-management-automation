<?php

namespace App\Filament\Resources\EmailThreads\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmailThreadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('subject')->disabled(),
                TextInput::make('gmail_thread_id')->disabled(),
                KeyValue::make('participants')->disabled(),
                DateTimePicker::make('last_message_at')->disabled(),
                TextInput::make('message_count')->numeric()->disabled(),
                Toggle::make('requires_reply')->disabled(),
                Toggle::make('requires_human_review')->disabled(),
            ]);
    }
}
