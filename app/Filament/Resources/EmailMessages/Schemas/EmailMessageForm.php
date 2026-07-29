<?php

namespace App\Filament\Resources\EmailMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmailMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sender_email')->disabled(),
                TextInput::make('subject')->disabled(),
                TextInput::make('gmail_message_id')->disabled(),
                TextInput::make('gmail_thread_id')->disabled(),
                DateTimePicker::make('received_at')->disabled(),
                KeyValue::make('labels')->disabled(),
                Textarea::make('snippet')->disabled()->columnSpanFull(),
                Textarea::make('text_body')->disabled()->columnSpanFull(),
                Toggle::make('is_read')->disabled(),
                Toggle::make('is_starred')->disabled(),
                Toggle::make('is_archived')->disabled(),
                Toggle::make('has_attachments')->disabled(),
            ]);
    }
}
