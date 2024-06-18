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

class BookingsExport implements
    FromCollection,
    WithColumnFormatting,
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    WithEvents
{
    use RegistersEventListeners;

    public $bookings;

    public function __construct($bookings)
    {
        $this->bookings = $bookings;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->bookings;
    }

    public function map($booking): array
    {
        $booking->load(['sessions.session', 'sessions.schedule']);
        $data = [
            "#{$booking->ref}",
            $booking->guest->details->full_name,
            $booking->guest->details->email,
            $booking->people(),
            $booking->sessions_count,
            $booking->booking_date->format('d.m.Y'),
            $booking->payment->total,
            $booking->payment->total_paid,
            strtoupper($booking->payment->methods ?? $booking->payment->records?->first()?->methods),
            $booking->sessions->map(function ($session) {
                return $session->full_name .' ('. $session->email .'): '. $session->date->format('d.m.Y') .' - '. $session->session->name .' '. $session->schedule->start_formatted .' - '. $session->schedule->end_formatted;
            })->implode("\n")
        ];

        return $data;
    }

    public function headings(): array
    {
        return [
            'REF',
            'BOOKER',
            'EMAIL',
            'TOTAL GUEST',
            'TOTAL SESSION',
            'BOOKED',
            'PRICE',
            'PAID',
            'METHOD',
            'SESSIONS',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_ACCOUNTING_EUR,
            'H' => NumberFormat::FORMAT_ACCOUNTING_EUR,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $cellRange = 'A1:J1';
        $headings = $event->sheet->getDelegate()->getStyle($cellRange);
        $headings->getFont()->setSize(14)->setBold(true);
        $headings->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rows = $event->sheet->getDelegate()->getStyle('A2:X500');
        $rows->getAlignment()->setVertical(Alignment::VERTICAL_TOP)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
    }
}
