<?php

namespace App\Exports\Booking;

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

class PaymentExport implements FromCollection, WithColumnFormatting, ShouldAutoSize, WithMapping, WithHeadings, WithEvents
{
    use RegistersEventListeners;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        return $this->payments->get();
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL,
            'B' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'N' => NumberFormat::FORMAT_CURRENCY_EUR,
            'O' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'P' => NumberFormat::FORMAT_TEXT,
            'Q' => NumberFormat::FORMAT_CURRENCY_EUR,
            'R' => NumberFormat::FORMAT_TEXT,
            'S' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $cellRange = 'A1:Q1';
        $headings = $event->sheet->getDelegate()->getStyle($cellRange);
        $headings->getFont()->setSize(14)->setBold(true);
        $headings->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rows = $event->sheet->getDelegate()->getStyle('A2:N700');
        $rows->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
        $event->sheet->getDelegate()->getStyle('M2:N700')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setWrapText(true);
    }

    public function map($payment): array
    {
        $booking = $payment->payment->booking;
        $verify = $payment->verified_at ? Date::dateTimeToExcel($payment->verified_at) : '---';
        $account_number = $payment->account_number ? '#'.$payment->account_number : '---';
        $unique_id = '---';

        if ($payment->methods == 'stripe') {
            $intent = $payment->stripe?->intent;
            $unique_id = $intent;
        }

        return [
            '#'.$booking->ref,
            $booking->id,
            $booking->guest->details->full_name,
            $booking->location->short_name,
            Date::dateTimeToExcel($booking->check_in),
            Date::dateTimeToExcel($booking->check_out),
            strtoupper($payment->methods),
            $payment->bank_name,
            $account_number,
            $payment->account_owner,
            $payment->iban_code,
            $unique_id,
            $payment->paid_at ? Date::dateTimeToExcel($payment->paid_at) : '-',
            $payment->user->name,
            $verify,
            $payment->payment->booking->parsePrice($payment->amount),
            $payment->payment->booking->parsePrice($payment->payment->total),
            $payment->payment->status,
            Date::dateTimeToExcel($payment->created_at),
        ];
    }

    public function headings(): array
    {
        return [
            'REF',
            'ID',
            'GUEST',
            'CAMP',
            'CHECK IN',
            'CHECK OUT',
            'METHOD',
            'BANK NAME',
            'ACC NUMBER',
            'ACC NAME',
            'IBAN CODE',
            'UNIQUE ID',
            'PAID AT',
            'VERIFIED BY',
            'VERIFIED AT',
            'AMOUNT',
            'TOTAL BOOKING PRICE',
            'STATUS',
            'CREATED AT',
        ];
    }
}
