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
                    ->label('Datum')
                    ->date('D, d. M y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('started_at')
                    ->label('Von')
                    ->time('H:i'),
                TextColumn::make('ended_at')
                    ->label('Bis')
                    ->time('H:i'),
                TextColumn::make('duration_hours')
                    ->label('Dauer (h)'),
                TextColumn::make('notes')
                    ->label('Notizen')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
