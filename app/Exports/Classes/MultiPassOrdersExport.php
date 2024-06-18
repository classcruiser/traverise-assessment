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

class MultiPassOrdersExport implements
    FromCollection,
    WithColumnFormatting,
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    WithEvents
{
    use RegistersEventListeners;

    public $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->orders;
    }

    public function map($order): array
    {
        $order->load('records');

        $data = [
            $order->ref,
            $order->multiPass->name,
            $order->guest->full_name ?? '-',
            $order->guest->email ?? '-',
            $order->total,
            $order->methods ?? '-',
            $order->status,
            $order->records->first()->paid_at ? $order->records->first()->paid_at->format('Y-m-d H:i:s') : null,
        ];

        return $data;
    }

    public function headings(): array
    {
        return [
            'REF',
            'MULTI PASS',
            'GUEST',
            'EMAIL',
            'AMOUNT',
            'PAYMENT METHOD',
            'STATUS',
            'DATE OF PURCHASE',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_GENERAL,
            'E' => NumberFormat::FORMAT_ACCOUNTING_EUR,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $cellRange = 'A1:H1';
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
