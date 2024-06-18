<?php

namespace App\Exports\Classes;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class GuestExport implements
    FromCollection,
    WithColumnFormatting,
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    WithEvents
{
    use RegistersEventListeners;

    public $guests;

    public function __construct($guests)
    {
        $this->guests = $guests;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->guests;
    }

    public function map($guest): array
    {
        $data = [
            "#{$guest->client_number}",
            $guest->full_name ?? '-',
            $guest->email ?? '-',
            $guest->phone ?? '-',
            $guest->country ?? '-',
            $guest->classes_count,
            $guest->passes_count,
        ];

        return $data;
    }

    public function headings(): array
    {
        return [
            'CLIENT ID',
            'FULL NAME',
            'EMAIL',
            'PHONE',
            'COUNTRY',
            'BOOKINGS',
            'MULTI PASSES'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_GENERAL,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $cellRange = 'A1:G1';
        $headings = $event->sheet->getDelegate()->getStyle($cellRange);
        $headings->getFont()->setSize(14)->setBold(true);
        $headings->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rows = $event->sheet->getDelegate()->getStyle('A2:X500');
        $rows->getAlignment()->setVertical(Alignment::VERTICAL_TOP)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
        $event->sheet->getDelegate()->getStyle('F2:G5000')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);
    }
}
