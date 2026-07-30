<?php

namespace App\Filament\Resources\EmailMessages\Schemas;

use App\Models\EmailMessage;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

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
                Placeholder::make('formatted_body')
                    ->label('Formatted body')
                    ->content(fn (?EmailMessage $record): HtmlString => new HtmlString(
                        $record
                            ? '<iframe src="'.e(route('email-messages.preview', $record)).'" sandbox="" style="width: 100%; min-height: 640px; border: 1px solid #e5e7eb; border-radius: 8px; background: white;"></iframe>'
                            : 'No message selected.',
                    ))
                    ->columnSpanFull(),
                Textarea::make('text_body')
                    ->label('Plain text body')
                    ->disabled()
                    ->rows(12)
                    ->columnSpanFull(),
                Toggle::make('is_read')->disabled(),
                Toggle::make('is_starred')->disabled(),
                Toggle::make('is_archived')->disabled(),
                Toggle::make('has_attachments')->disabled(),
            ]);
    }
}
