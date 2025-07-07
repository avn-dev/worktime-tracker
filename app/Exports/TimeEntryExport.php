<?php

namespace App\Exports;

use App\Models\TimeEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TimeEntryExport implements FromCollection, WithMapping, WithHeadings, WithColumnWidths, WithEvents
{
    protected Collection $entries;
    protected int $lastDataRow = 0;
    protected bool $includeNotes;

    public function __construct(bool $includeNotes = true)
    {
        $this->includeNotes = $includeNotes;
    }

    public function collection(): Collection
    {
        $this->entries = TimeEntry::query()
            ->where('user_id', Auth::id())
            ->orderBy('day')
            ->orderBy('started_at')
            ->get(['day', 'started_at', 'ended_at', 'duration_hours', 'notes']);

        $totalDuration = $this->entries->sum('duration_hours');

        $rows = $this->entries;

        $rows->push((object)[
            'day' => null,
            'started_at' => null,
            'ended_at' => null,
            'duration_hours' => null,
            'notes' => null,
        ]);

        $rows->push((object)[
            'day' => 'Gesamtsumme:',
            'started_at' => '',
            'ended_at' => '',
            'duration_hours' => $totalDuration,
            'notes' => null,
        ]);

        return $rows;
    }


    public function map($entry): array
    {
        static $lastDay = null;

        if ($entry->day === 'Gesamtsumme:') {
            $this->lastDataRow++;
            return [
                'Gesamtsumme:',
                '',
                '',
                (float) $entry->duration_hours,
                $this->includeNotes ? '' : null,
            ];
        }

        if (!$entry->day && !$entry->started_at) {
            $this->lastDataRow++;
            return ['', '', '', '', ''];
        }

        $dayFormatted = Carbon::parse($entry->day)->format('d.m.Y');
        $showDay = $dayFormatted !== $lastDay ? $dayFormatted : '';

        $row = [
            $showDay,
            Carbon::parse($entry->started_at)->format('H:i'),
            Carbon::parse($entry->ended_at)->format('H:i'),
            (float) $entry->duration_hours,
        ];

        if ($this->includeNotes) {
            $row[] = $entry->notes;
        }

        $lastDay = $dayFormatted;
        $this->lastDataRow++;

        return $row;
    }


    public function headings(): array
    {
        $this->lastDataRow = 1;

        $headings = ['Datum', 'Von', 'Bis', 'Dauer (h)'];
        if ($this->includeNotes) {
            $headings[] = 'Notizen';
        }

        return $headings;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->getStyle('D1:D' . $this->lastDataRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle('A' . $this->lastDataRow . ':D' . $this->lastDataRow)
                    ->getFont()
                    ->setBold(true);
            },
        ];
    }
}
