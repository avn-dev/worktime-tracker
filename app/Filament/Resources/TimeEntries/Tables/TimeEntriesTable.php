<?php

namespace App\Filament\Resources\TimeEntries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class TimeEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day')
                    ->date('D, d. M Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('started_at')
                    ->time('H:i')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ended_at')
                    ->time('H:i')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('duration_hours')
                    ->label('Duration (hours)')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('notes')
                    ->limit(50)
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
