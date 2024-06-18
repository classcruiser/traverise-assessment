<?php

namespace App\Exports\Booking;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class IncomeExport implements FromCollection, WithColumnFormatting, ShouldAutoSize, WithMapping, WithHeadings, WithEvents
{
    use RegistersEventListeners;

    public function __construct($data)
    {
        $this->data = $data;
        $this->date = Carbon::now();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    public function map($data): array
    {
        return [
            '#'. $data[0],
            $data[1],
            $data[2],
            $data[3],
            $data[4],
            $data[5],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_CURRENCY_EUR,
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $cellRange = 'A1:F1';
        $headings = $event->sheet->getDelegate()->getStyle($cellRange);
        $headings->getFont()->setSize(14)->setBold(true);
        $headings->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rows = $event->sheet->getDelegate()->getStyle('A2:F500');
        $rows->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
        $event->sheet->getDelegate()->getStyle('B2:B500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
        $event->sheet->getDelegate()->getStyle('C2:C500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
        $event->sheet->getDelegate()->getStyle('D2:D500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
        $event->sheet->getDelegate()->getStyle('E2:E500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
    }

    public function headings(): array
    {
        return [
            'Booking Ref',
            'Guest Name',
            'Check In',
            'Check Out',
            'Location',
            'Income',
        ];
    }
}
