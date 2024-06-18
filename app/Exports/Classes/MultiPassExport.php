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

class MultiPassExport implements
    FromCollection,
    WithColumnFormatting,
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    WithEvents
{
    use RegistersEventListeners;

    public $multiPasses;

    public function __construct($multiPasses)
    {
        $this->multiPasses = $multiPasses;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->multiPasses;
    }

    public function map($multiPass): array
    {
        $percentage = $multiPass->amount_type == 'PERCENTAGE' ? '%' : '';
        $currency = $multiPass->amount_type == 'VALUE' ? '€' : '';
        $amount = match ($multiPass->type) {
            'CREDIT' => "€ {$multiPass->amount}",
            'SESSION' => "{$multiPass->amount} session",
            default => "{$currency} {$multiPass->amount}{$percentage}",
        };

        $codeGeneratedAt = null;
        if ($multiPass->type === 'VOUCHER' && $multiPass->code) {
            $codeGeneratedAt = $multiPass->code_generated_at?->format('Y-m-d') ?? $multiPass->created_at->format('Y-m-d');
        }

        $data = [
            $multiPass->name,
            $multiPass->type ?? '-',
            $multiPass->code ?? '-',
            $codeGeneratedAt,
            $multiPass->class_session_id ? $multiPass->session->category->name . ' -> ' . $multiPass->session?->name : '-',
            $amount,
            $multiPass->price <= 0 ? 'FREE' : '€ ' . number_format($multiPass->price),
            $multiPass->is_active ? 'Y' : 'N',
            number_format($multiPass->usage)
        ];

        return $data;
    }

    public function headings(): array
    {
        return [
            'NAME',
            'TYPE',
            'CODE',
            'GENERATED AT',
            'LIMIT TO',
            'AMOUNT',
            'PRICE',
            'ACTIVE',
            'USAGE'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => NumberFormat::FORMAT_GENERAL,
            'F' => NumberFormat::FORMAT_GENERAL,
            'G' => NumberFormat::FORMAT_ACCOUNTING_EUR,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $cellRange = 'A1:I1';
        $headings = $event->sheet->getDelegate()->getStyle($cellRange);
        $headings->getFont()->setSize(14)->setBold(true);
        $headings->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rows = $event->sheet->getDelegate()->getStyle('A2:X500');
        $rows->getAlignment()->setVertical(Alignment::VERTICAL_TOP)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
        $event->sheet->getDelegate()->getStyle('F2:I5000')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);
    }
}
