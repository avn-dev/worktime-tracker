<?php

namespace App\Filament\Resources\TimeEntries\Schemas;

use Dom\Text;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Auth;

class TimeEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn() => Auth::id())
                    ->dehydrated(),
                DatePicker::make('day')
                    ->native(false)
                    ->required()
                    ->default(now())
                    ->minDate(now()->subMonths(6))
                    ->maxDate(now()),
                TimePicker::make('started_at')
                    ->native(false)
                    ->required()
                    ->default('8:00')
                    ->minutesStep(15)
                    ->seconds(false),
                TimePicker::make('ended_at')
                    ->native(false)
                    ->required()
                    ->default('17:00')
                    ->minutesStep(15)
                    ->seconds(false),
                TextInput::make('duration_hours')
                    ->label('Duration (hours)')
                    ->disabled()
                    ->required(),
                TextInput::make('notes')
                    ->placeholder('Optional notes about this time entry')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ])
            ->columns(4);
    }
}
