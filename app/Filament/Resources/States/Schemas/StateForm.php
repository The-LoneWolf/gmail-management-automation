<?php

namespace App\Filament\Resources\States\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StateForm
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
                TextInput::make('slug')->required(),
                Textarea::make('description')->columnSpanFull(),
                TextInput::make('type')->default('workflow')->required(),
                TextInput::make('sort_order')->numeric()->default(0),
                ColorPicker::make('color')->default('#64748b'),
                Toggle::make('is_initial'),
                Toggle::make('is_final'),
                Toggle::make('is_active')->default(true),
            ]);
    }
}
