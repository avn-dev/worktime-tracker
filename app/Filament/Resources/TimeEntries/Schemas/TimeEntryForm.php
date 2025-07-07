<?php

namespace App\Filament\Resources\TimeEntries\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;

class TimeEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Hidden::make('user_id')
                    ->default(fn () => Auth::id())
                    ->dehydrated(),

                DatePicker::make('day')
                    ->label('Datum')
                    ->native(false)
                    ->required()
                    ->default(now())
                    ->minDate(now()->subMonths(6))
                    ->maxDate(now()),

                TimePicker::make('started_at')
                    ->label('Von')
                    ->native(false)
                    ->required()
                    ->default('08:00')
                    ->minutesStep(15)
                    ->seconds(false)
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $set('duration_hours', TimeEntryForm::calculateDuration(
                            $get('started_at'),
                            $get('ended_at'),
                        ));
                    }),

                TimePicker::make('ended_at')
                    ->label('Bis')
                    ->native(false)
                    ->required()
                    ->default('17:00')
                    ->minutesStep(15)
                    ->seconds(false)
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $set('duration_hours', TimeEntryForm::calculateDuration(
                            $get('started_at'),
                            $get('ended_at'),
                        ));
                    }),

                TextInput::make('duration_hours')
                    ->label('Dauer (h)')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->afterStateHydrated(function (TextInput $component, $state, Get $get) {
                        $component->state(
                            TimeEntryForm::calculateDuration($get('started_at'), $get('ended_at'))
                        );
                    }),

                TextInput::make('notes')
                    ->label('Notizen')
                    ->placeholder('Optional notes about this time entry')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ])
            ->columns(4);
    }

    private static function calculateDuration($start, $end): ?string
    {
        if (!$start || !$end) {
            return null;
        }

        try {
            $start = is_string($start) ? Carbon::parse($start) : Carbon::instance($start);
            $end = is_string($end) ? Carbon::parse($end) : Carbon::instance($end);

            if ($end->lessThan($start)) {
                $end->addDay();
            }

            return number_format($start->diffInMinutes($end) / 60, 2);
        } catch (\Throwable) {
            return null;
        }
    }
}
