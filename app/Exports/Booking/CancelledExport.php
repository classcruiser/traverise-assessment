<?php

namespace App\Exports\Booking;

use App\Models\Booking;
use App\Models\Payment;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class CancelledExport implements FromCollection, WithColumnFormatting, ShouldAutoSize, WithMapping, WithHeadings, WithEvents
{
  use RegistersEventListeners;

  public function __construct($bookings)
  {
    $this->bookings = $bookings;
  }

  public function collection()
  {
    return $this->bookings->get();
  }

  public function columnFormats(): array
  {
    return [
      'A' => NumberFormat::FORMAT_GENERAL,
      'B' => NumberFormat::FORMAT_NUMBER,
      'C' => NumberFormat::FORMAT_TEXT,
      'D' => NumberFormat::FORMAT_TEXT,
      'E' => NumberFormat::FORMAT_GENERAL,
      'F' => NumberFormat::FORMAT_GENERAL,
      'G' => NumberFormat::FORMAT_CURRENCY_EUR,
      'H' => NumberFormat::FORMAT_TEXT,
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

    $rows = $event->sheet->getDelegate()->getStyle('A2:J200');
    $rows->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
  }

  public function map($booking): array
  {
    $cancel_date = $booking->histories->where('action', 'Cancel booking')->first()['created_at'];
    $notes = $booking->histories->where('action', 'Cancel booking')->first()['details'] .' on '. $cancel_date->format('d.m.Y H:i');

    return [
      '#'. $booking->ref,
      $booking->id,
      $booking->guest->details->full_name,
      $booking->location->short_name,
      $booking->check_in->format('d.m.Y'),
      $booking->check_out->format('d.m.Y'),
      $booking->grand_total,
      strip_tags($booking->cancel_reason),
      strip_tags($notes),
      $cancel_date->format('d.m.Y'),
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
      'PRICE',
      'REASON',
      'LOG',
      'CANCELLATION DATE'
    ];
  }
}
