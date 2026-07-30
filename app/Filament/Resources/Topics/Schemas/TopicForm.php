<?php

namespace App\Filament\Resources\Topics\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TopicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'email')
                    ->default(fn (): ?int => Auth::id())
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set): mixed => $set('slug', Str::slug((string) $state))),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TagsInput::make('keywords')
                    ->separator(',')
                    ->columnSpanFull(),
                TagsInput::make('examples')
                    ->separator(',')
                    ->columnSpanFull(),
                ColorPicker::make('color')
                    ->default('#2563eb'),
                TextInput::make('minimum_confidence')
                    ->numeric()
                    ->default(0.85)
                    ->minValue(0)
                    ->maxValue(1),
                Toggle::make('requires_human_review'),
                Toggle::make('is_active')->default(true),
            ]);
    }
}
