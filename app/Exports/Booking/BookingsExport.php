<?php

namespace App\Exports\Booking;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Services\Booking\BookingService;
use App\Services\UtilService;

class BookingsExport implements
    FromCollection,
    WithColumnFormatting,
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    WithEvents
{
    use RegistersEventListeners;
    use Exportable;

    public $bookings;
    public $bookingService;
    public $questionnaires;
    public $date;
    private static $questionCount;

    public function __construct($bookings, BookingService $bookingService, $questionnaires)
    {
        $this->bookings = $bookings;
        $this->bookingService = $bookingService;
        $this->questionnaires = $questionnaires;
        $this->date = Carbon::now();

        self::$questionCount = $questionnaires->count();
    }

    public function collection()
    {
        return $this->bookings->get();
    }

    public function columnFormats(): array
    {
        $alpha = 'S';
        $columnFormats = [
            'A' => NumberFormat::FORMAT_GENERAL,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_ACCOUNTING_EUR,
            'O' => NumberFormat::FORMAT_ACCOUNTING_EUR,
            'P' => NumberFormat::FORMAT_ACCOUNTING_EUR,
            'Q' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_ACCOUNTING_EUR,
            'U' => NumberFormat::FORMAT_ACCOUNTING_EUR,
        ];

        # for questionnaire
        for ($i = 0; $i < $this->questionnaires->count(); $i++) {
            ++$alpha;
            $columnFormats[$alpha] = NumberFormat::FORMAT_TEXT;
        }

        # for the rest columns
        for ($i = 0; $i < 7; $i++) {
            ++$alpha;
            $format = NumberFormat::FORMAT_ACCOUNTING_EUR;
            if ($i == 5) {
                $format = NumberFormat::FORMAT_TEXT;
            }

            $columnFormats[$alpha] = $format;
        }

        return $columnFormats;
    }

    public static function afterSheet(AfterSheet $event)
    {
        $lastCellHeader = 'V';
        $totalHeader = (self::$questionCount + 17);
        for ($i = 0; $i < $totalHeader; $i++) {
            ++$lastCellHeader;
        }

        $cellRange = "A1:{$lastCellHeader}1";
        $headings = $event->sheet->getDelegate()->getStyle($cellRange);
        $headings->getFont()->setSize(14)->setBold(true);
        $headings->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rows = $event->sheet->getDelegate()->getStyle('A2:X500');
        $rows->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setWrapText(true);
        $event->sheet->getDelegate()->getStyle('B1:B500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);
        $event->sheet->getDelegate()->getStyle('D1:D500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);
        $event->sheet->getDelegate()->getStyle('M1:L500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);
        //$event->sheet->getDelegate()->getStyle('N2:N500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setWrapText(true);

        $nextAlpha = 'V';
        for ($i = 0; $i < self::$questionCount; $i++) {
            ++$nextAlpha;
        }
        $nextAlphaAfter = UtilService::stringDecrement($nextAlpha);
        $event->sheet->getDelegate()->getStyle("{$nextAlpha}1:{$nextAlphaAfter}500")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);

        $nextAlpha = 'V';
        for ($i = 0; $i < self::$questionCount; $i++) {
            ++$nextAlpha;
        }
        $nextAlphaAfter = $nextAlpha;
        for ($i = 0; $i < 6; $i++) {
            ++$nextAlphaAfter;
        }
        $event->sheet->getDelegate()->getStyle("{$nextAlpha}2:{$nextAlphaAfter}500")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setWrapText(true);
    }

    public function map($booking): array
    {
        $guests = collect([]);
        $payments = collect([]);
        $nights = 0;
        $average = 0;

        if ($this->date->greaterThan($booking->check_out) && $nights > 0) {
            $average = floatval($booking->grand_total / $nights);
        }

        $taxes = [
            'cultural_tax_percent' => $booking->location->cultural_tax,
            'hotel_tax_percent' => $booking->location->hotel_tax,
            'goods_tax_percent' => $booking->location->goods_tax,
            'hotel_tax' => $this->bookingService->calculateTax(
                $booking->location->hotel_tax,
                $booking->subtotal + $booking->payment->processing_fee
            ),
            'goods_tax' => $this->bookingService->calculateTax(
                $booking->location->goods_tax,
                $booking->total_addons_price
            ),
            'cultural_tax' => $booking->room_tax,
        ];

        $last_booking = null;
        $same_booking = false;

        if ($booking->guests_count > 0) {
            foreach ($booking->guests as $room_guest) {
                foreach ($room_guest->rooms as $booking_room_guest) {
                    $br_room = $booking_room_guest->room;
                    $room_name = $br_room->subroom->name;
                    $nights = (int) $br_room->nights;
                    $same_booking = (bool) $last_booking == $booking->ref;

                    $addons = $br_room->addons;

                    if (request()->has('has_addons') && count(request('has_addons')) == 1) {
                        $addons = $br_room->addons->filter(function ($addon) {
                            return $addon->extra_id == request('has_addons')[0];
                        });
                    }

                    $data = [
                        !$same_booking ? '#' . $booking->ref : '',
                        $room_guest->details->full_name,
                        $room_guest->details->email,
                        ($room_guest->details->phone ? 'P: ' . $room_guest->details->phone : ''),
                        !$same_booking ? $booking->total_guests : '',
                        !$same_booking ? $booking->booking_status : '',
                        !$same_booking ? $booking->location->name : '',
                        !$same_booking ? $booking->created_at->format('Y-m-d') : '',
                        !$same_booking ? $booking->check_in->format('Y-m-d') : '',
                        !$same_booking ? $booking->check_out->format('Y-m-d') : '',
                        $nights,
                        $room_name . ($br_room->is_private ? ' (PRIVATE)' : ''),
                        $br_room->bed_type,
                        number_format($br_room->price, 2),
                        number_format($taxes['hotel_tax'], 2),
                        number_format($taxes['cultural_tax'], 2),
                        !$same_booking && $booking->discounts ? $booking->discounts->map(
                            function ($discount) use ($booking) {
                                if ('Percent' == $discount->type) {
                                    $discSource = $booking->subtotal;
                                    if ('ALL' == $discount->apply_to) {
                                        $discSource = $booking->total_price;
                                    }

                                    return $discount->name . ' - €' .
                                        floatval($booking->parsePrice(
                                            round($discSource * ($discount->value / 100), 2)
                                        ));
                                }

                                return $discount->name . ' - €' .
                                    floatval($booking->parsePrice(round($discount->value)));
                            }
                        )->implode("\n") : '',
                        !$same_booking && $booking->discounts ? $booking->discounts->sum(function ($discount) use ($booking) {
                            if ('Percent' == $discount->type) {
                                $discSource = $booking->subtotal;
                                if ('ALL' == $discount->apply_to) {
                                    $discSource = $booking->total_price;
                                }

                                return round($discSource * ($discount->value / 100), 2);
                            }

                            return round($discount->value);
                        }) : '',
                        $addons ? $addons->sum(fn ($addon) => $addon->amount) : 0,
                        $addons ? $addons->map(function ($addon) use ($booking) {
                            return $addon->details->name . " (€ ". $booking->parsePrice($addon->price) . ")";
                        })->implode("\n") : [],
                        $addons ? $addons->sum(fn ($addon) => $addon->price) : 0,
                    ];

                    if ($this->questionnaires->count() > 0) {
                        $addonQuestions = [];
                        foreach ($br_room->addons as $addon) {
                            if (is_array($addon->questionnaire_answers) && $addon->details->questionnaire) {
                                $addonQuestions[strtolower($addon->details->questionnaire->title)] =
                                    implode(', ', $addon->questionnaire_answers);
                            }
                        }

                        foreach ($this->questionnaires as $title) {
                            $val = '';
                            if (isset($addonQuestions[strtolower($title)])) {
                                $val = $addonQuestions[strtolower($title)];
                            }

                            $data[] = $val;
                        }
                    }

                    $data = array_merge($data, [
                        !$same_booking ? number_format($taxes['goods_tax'], 2) : '',
                        $booking->transfers ? $booking->transfers->map(function ($transfer) use ($booking) {
                            return $transfer->details->name . ' for ' . $transfer->guests . ' guests (' .
                                $transfer->flight_number . ' ' .
                                ($transfer->flight_time ? $transfer->flight_time->format('d.m.Y H:i:s') : '') .
                                ' (€ ' . $booking->parsePrice($transfer->price) . ')';
                        })->implode("\n") : '',
                        !$same_booking ? floatval($booking->parsePrice($booking->payment->total)) : '',
                        !$same_booking ? (number_format($taxes['hotel_tax'] + $taxes['goods_tax'], 2)) : '',
                        !$same_booking ? $booking->totalExtensionPrice() : '',
                        !$same_booking ? floatval(
                            $booking->agent_commission ? $booking->parsePrice($booking->agent_commission) : 0
                        ) : '',
                        !$same_booking ? floatval(
                            ('' != $booking->payment->open_balance && 0 != $booking->payment->open_balance)
                                ? $booking->parsePrice($booking->payment->open_balance)
                                : 0
                        ) : '',
                        !$same_booking ? $booking->channel : '',
                        !$same_booking ? $booking->opportunity : '',
                        !$same_booking ? $payments->pluck('record')->implode("\n") : '',
                        !$same_booking ? $booking->parsePrice($average) : '',
                        !$same_booking ? $booking->guest->details->country : '',
                        !$same_booking ? $booking->origin : '',
                        !$same_booking ? $booking->agent->name : '',
                        !$same_booking ? ($booking->source_type != 'Guest' ? $booking->user?->name : 'Guest') : '',
                        !$same_booking ? $booking->payment->methods : '',
                        !$same_booking ? $booking->payment->processing_fee : '',
                    ]);

                    $guests->push($data);

                    $last_booking = $booking->ref;
                }
            }
        }

        if ($booking->payment->records_count > 0) {
            foreach ($booking->payment->records as $record) {
                if ($record->verify_by) {
                    $payments->push([
                        'record' => $record->user->name . ' verified payment €' .
                            floatval($booking->parsePrice($record->amount)) . ' (' . $record->methods . ') on ' .
                            $record->verified_at->format('d.m.Y H:i:s'),
                    ]);
                }
            }
        } else {
            $payments->push([
                'record' => 'No record',
            ]);
        }

        return $guests->all();
    }

    public function headings(): array
    {
        $headers = [
            'REF',
            'GUEST',
            'EMAIL',
            'PHONE',
            'TOTAL GUEST',
            'STATUS',
            'CAMP',
            'BOOKED',
            'CHECK IN',
            'CHECK OUT',
            'TOTAL NIGHTS',
            'ROOMS',
            'BED',
            'ROOM PRICE',
            'HOTEL TAX 7%',
            'CULTURAL TAX 5%',
            'DISCOUNT',
            'DISCOUNT AMOUNT',
            'ADD-ONS COUNT',
            'ADD-ONS',
            'ADD-ONS PRICE',
        ];

        foreach ($this->questionnaires as $title) {
            $headers[] = strtoupper($title);
        }

        $headers = array_merge(
            $headers,
            [
                'GOODS TAX 19%',
                'TRANSFERS',
                'PRICE',
                'TOTAL TAX',
                'EXTENSION',
                'COMMISSION',
                'OPEN BALANCE',
                'CHANNEL',
                'OPPORTUNITY',
                'PAYMENT HISTORY',
                'AVERAGE SPENT',
                'COUNTRY',
                'DOMAIN',
                'AGENT',
                'ADDED BY',
                'PAYMENT METHOD',
                'PROCESSING FEE'
            ]
        );

        return $headers;
    }
}
