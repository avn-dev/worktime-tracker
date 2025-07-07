<?php

namespace App\Filament\Resources\TimeEntries\Pages;

use App\Filament\Resources\TimeEntries\TimeEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use Illuminate\Support\Facades\Auth;
use App\Exports\TimeEntryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\TimeEntry;

class ListTimeEntries extends ListRecords
{
    protected static string $resource = TimeEntryResource::class;

    protected function getHeaderActions(): array
    {

        return [
            CreateAction::make(),
            ExportAction::make('export')
            ->label('Export als Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->action(function () {
                $userId = Auth::id();

                $hasNotes = TimeEntry::query()
                    ->where('user_id', $userId)
                    ->whereNotNull('notes')
                    ->where('notes', '!=', '')
                    ->exists();

                $export = new TimeEntryExport(includeNotes: $hasNotes);

                return Excel::download($export, 'time-entries.xlsx');
            }),
        ];
    }
}
