@php
use App\Services\Booking\TaxService;

$tax_totals = [
    'hotel' => 0,
    'addons' => 0,
];

@endphp
<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>Invoice Booking {{$booking->ref}}</title>
    <style type="text/css">
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        font-size: 9pt;
        background: #fff;
        margin: 10px;
    }

    div.absolute {
        position: absolute;
        vertical-align: middle;
    }

    table.invoice {
        width: 100%;
        border: 1px solid #ccc;
        font-size: 8pt;
    }

    table.invoice tr th {
        padding: 5px 10px;
        background-color: #eee;
        font-size: 8pt;
        border-bottom: 1px solid #ccc;
    }

    table.invoice tr td {
        padding: 5px 10px;
        border-right: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        font-size: 8pt;
    }

    .text-right {
        text-align: right;
    }
    </style>
</head>

<body>

    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 9pt" class="table">
        <tr>
            <td align="left" width="40%" valign="top">
                <div style="height: 122px;">
                    @if(file_exists(public_path('tenancy/assets/images/camps/'. tenant('id') .'_logo.jpg')))
                        <img src="{{ public_path('tenancy/assets/images/camps/'. tenant('id') .'_logo.jpg' )}}" alt="{{$profile->title}}" style="width: 200px" />
                    @else
                        <h2>{{$profile->title}}</h2>
                    @endif
                </div>
            </td>
            <td align="right" valign="top" width="60%">
                <p>
                    <span style="font-size: 10pt">Booking ref: <b>{{$booking->ref}}</b></span><br />
                    @if ($booking->special_package_id)
                        Package: <b>{{ $booking->specialPackage->name }}</b><br />
                    @endif
                    Created at: <b>{{$booking->created_at->format('d.m.Y, H:i')}}</b><br />
                    State: <b>{{$booking->booking_status}}</b><br />
                    @if($booking->payment->invoice)
                        Invoice Ref:
                        @if ($invoice_number)
                            <b>{{ $invoice_number}}</b>
                        @else
                            <b>{{ request()->has('CANCELLATION') ? 'CC-' : '' }}{{strtoupper($booking->payment->invoice)}}</b>
                        @endif
                    @else
                        @if ($booking->status == 'CANCELLED')
                            Invoice Ref: <b>CCL-{{ $booking->ref }}</b>
                        @endif
                    @endif
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px 0; text-align: center; font-size: 12pt; font-weight: bold;" colspan="2">
                @if (request()->has('CANCELLATION'))
                    Invoice correction<br />
                    (Rechnungskorrektur)
                @else
                    Invoice &amp; Booking Confirmation
                @endif
            </td>
        </tr>
        <tr>
            <td valign="top">
                <p style="font-size: 11pt"><b>Customer details:</b></p>
                <p>
                    <b>{{$booking->guest->details->full_name}}</b>
                    <br />
                    @if($booking->guest->details->company != '' && $booking->guest->details->company != '---')
                        Company: <b>{{$booking->guest->details->company}}</b><br />
                    @endif
                    @if($booking->guest->details->street != '' && $booking->guest->details->street != '---')
                        Street: <b>{{$booking->guest->details->street}}</b><br />
                    @endif
                    @if($booking->guest->details->city != '' && $booking->guest->details->city != '---')
                        City: <b>{{$booking->guest->details->city}}</b><br />
                    @endif
                    @if($booking->guest->details->zip != '' && $booking->guest->details->zip != '---')
                        Postal / Zip Code: <b>{{$booking->guest->details->zip}}</b><br />
                    @endif
                    @if($booking->guest->details->country != '' && $booking->guest->details->country != '---')
                        Country: <b>{{$booking->guest->details->country}}</b><br />
                    @endif
                    @if($booking->guest->details->email != '' && $booking->guest->details->email != '---')
                        Email: <b>{{strtolower($booking->guest->details->email)}}</b><br />
                    @endif
                    @if($booking->guest->details->phone != '' && $booking->guest->details->phone != '---')
                        Phone: <b>{{$booking->guest->details->phone}}</b><br />
                    @endif
                </p>
            </td>
            <td valign="top">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="50%" valign="top">
                            <p style="font-size: 11pt"><b>Destination details:</b></p>
                            <p>
                                <b>{{$booking->location->name}}</b><br />
                                {!! $booking->location->address !!}
                            </p>
                        </td>
                        <td width="50%" align="right">
                            @if (!request()->has('CANCELLATION'))
                                <img src="{{ public_path('qr/'. $booking->ref .'-'. $booking->id .'.png') }}" style="width: 100px; height: 100px; margin-bottom: 1rem; display: block;" />
                            @else
                                <img src="{{ public_path('images/cancelled.png') }}" alt="CANCELLED" style="width: 100px; display: block;" />
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @php
        $rowspan = $tax['inclusives']['total'] > 0 ? 2 : 1;
        $colspan_totals = 5 + $tax['inclusives']['total'];
        $totals = [];
    @endphp

    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="invoice">
        <thead>
            <tr class="alpha-grey">
                <th class="text-uppercase p-3" rowspan="{{ $rowspan }}" style="border-right: 1px solid #ccc" width="13%">Customer</th>
                <th class="text-uppercase p-3" rowspan="{{ $rowspan }}" style="border-right: 1px solid #ccc;" width="20%">Product/Package</th>
                <th class="text-uppercase p-3" rowspan="{{ $rowspan }}" style="border-right: 1px solid #ccc" width="15%">Check In/Check Out</th>
                <th class="text-uppercase p-3" rowspan="{{ $rowspan }}" style="border-right: 1px solid #ccc" width="10%">Qty</th>
                <th class="text-uppercase p-3 text-right" style="min-width: 50px; border-right: 1px solid #ccc" rowspan="{{ $rowspan }}" width="10%">Subtotal</th>
                @if ($tax['inclusives']['total'] > 0)
                    <th class="text-uppercase p-3 text-center" colspan="{{ $tax['inclusives']['total'] }}" style="border-right: 1px solid #ccc">TAX &amp; SERVICE</th>
                @endif
                <th class="text-uppercase p-3 text-right" rowspan="{{ $rowspan }}" width="12%">TOTAL</th>
            </tr>
            @if ($tax['inclusives']['total'] > 0)
                <tr class="alpha-grey">
                    @foreach ($tax['inclusives']['taxes'] as $inc_tax)
                        <th class="text-uppercase p-3 text-center" style="border-right: 1px solid #ccc" width="{{ round(20 / $tax['inclusives']['total']) }}%">
                            {{ number_format($inc_tax->rate, 0) }}%<br />{{ strtoupper($inc_tax->name) }}
                        </th>
                        @php
                            $totals['tax-'. $inc_tax->id] = 0;
                        @endphp
                    @endforeach
                </tr>
            @endif
        </thead>
        <tbody>
            @if($booking->rooms_count)
                @foreach($booking->rooms as $r)
                    @php
                        $borderTop = !$loop->first ? 'style="border-top: 2px solid #222;"' : '';
                        $borderTopNoRight = !$loop->first ? 'style="border-top: 2px solid #222; border-right: 0;"' : 'style="border-right: 0"';
                        $room_tax = $r->room->taxes?->first();
                    @endphp
                    <tr>
                        <td {!! $borderTop !!}>{{ $r->guestDetails->details->details->full_name }}</td>
                        <td {!! $borderTop !!}>
                            {{ $role == 4 ? $r->subroom->agent_name : $r->subroom->name }} {!! $r->is_private ? '(Private)' : '' !!} {{ $r->bed_type }}
                        </td>
                        <td {!! $borderTop !!}>{{date('d.m.Y', strtotime($r->from))}} - {{date('d.m.Y', strtotime($r->to))}}</td>
                        <td {!! $borderTop !!}>{{$r->days}} days / {{$r->nights}} nights</td>
                        <td class="text-right" {!! $borderTop !!}>
                            @if ($room_tax && $room_tax->model_id == $r->room_id)
                                &euro; {{ TaxService::getAmountWithoutTax($r->price, $r->room->taxes?->first()->tax->rate) }}
                            @else
                                &euro; {{ parsePrice($r->price) }}
                            @endif
                        </td>

                        @foreach ($tax['inclusives']['taxes'] as $inc_tax)
                            <td class="text-right" {!! $borderTop !!}>
                                @if ($room_tax && $room_tax->custom_tax_id == $inc_tax->id)
                                    &euro; {{ TaxService::calculateTax($r->price, $inc_tax->rate) }}
                                    @php
                                        $totals['tax-'. $inc_tax->id] += TaxService::calculateTax($r->price, $inc_tax->rate);
                                    @endphp
                                @endif
                            </td>
                        @endforeach

                        <td {!! $borderTopNoRight !!} class="text-right">
                            &euro; {{ parsePrice($r->price) }}
                        </td>
                    </tr>
                    @if($booking->location->duration_discount)
                        <tr>
                            <td colspan="{{ $colspan_totals }}">&rsaquo; Duration Discount</td>
                            <td class="text-right">- &euro; {{floatVal($booking->parsePrice($r->duration_discount))}}</td>
                        </tr>
                    @endif
                    @if($r->discounts() && !$booking->archived)
                        @foreach($r->discounts as $offer)
                            <tr>
                                <td colspan="{{ $colspan_totals }}">
                                    &rsaquo; Special Offer:
                                    {{$offer->offer->name}} ({!! $offer->offer->discount_type == 'Percent' ? $offer->offer->discount_value .'%' : '&euro;'. $booking->parsePrice($offer->offer->discount_value) !!})
                                </td>
                                <td class="text-right">- &euro; {{floatVal($booking->parsePrice($offer->discount_value))}}</td>
                            </tr>
                        @endforeach
                    @endif
                    @forelse($r->addons as $addon)
                        @php
                            $addon_tax = $addon->details->taxes?->first();
                        @endphp
                        <tr>
                            <td colspan="3">&rsaquo; {{$addon->details->name}}</td>
                            <td class="text-left">
                                @if($addon->details->rate_type == 'Day')
                                    {{ intVal($addon->amount) }} {{ $addon->details->unit_name }}
                                @endif
                            </td>
                            <td class="text-right">
                                @if ($addon_tax && $addon_tax->model_id == $addon->extra_id)
                                    &euro; {{ TaxService::getAmountWithoutTax($addon->price, $addon_tax->tax->rate) }}
                                @else
                                    &euro; {{ parsePrice($addon->price) }}
                                @endif
                            </td>
                            @foreach ($tax['inclusives']['taxes'] as $inc_tax)
                                <td class="text-right">
                                    @if ($addon_tax && $addon_tax->custom_tax_id == $inc_tax->id)
                                        &euro; {{ TaxService::calculateTax($addon->price, $inc_tax->rate) }}
                                        @php
                                            $totals['tax-'. $inc_tax->id] += TaxService::calculateTax($addon->price, $inc_tax->rate);
                                        @endphp
                                    @endif
                                </td>
                            @endforeach
                            <td style="border-right: 0;" class="text-right">&euro; {{ parsePrice($addon->price) }}</td>
                        </tr>
                    @empty
                    @endforelse
                @endforeach
                @forelse($booking->transfers as $transfer)
                    @php
                        $transfer_tax = $transfer->details->taxes?->first();
                    @endphp
                    <tr>
                        <td colspan="3">
                            &rsaquo; {{ $transfer->details->name }}
                            {!! $transfer->flight_detail !!}
                        </td>
                        <td class="text-center">{{$transfer->guests .' '. \Illuminate\Support\Str::plural('guest', $transfer->guests)}}</td>
                        <td class="text-right">
                            @if ($transfer_tax && $transfer_tax->model_id == $transfer->transfer_extra_id)
                                &euro; {{ TaxService::getAmountWithoutTax($transfer->price, $transfer_tax->tax->rate) }}
                            @else
                                &euro; {{ parsePrice($transfer->price) }}
                            @endif
                        </td>
                        @foreach ($tax['inclusives']['taxes'] as $inc_tax)
                            <td class="text-right">
                                @if ($transfer_tax && $transfer_tax->custom_tax_id == $inc_tax->id)
                                    &euro; {{ TaxService::calculateTax($transfer->price, $inc_tax->rate) }}
                                    @php
                                        $totals['tax-'. $inc_tax->id] += TaxService::calculateTax($transfer->price, $inc_tax->rate);
                                    @endphp
                                @endif
                            </td>
                        @endforeach
                        <td style="border-right: 0;" class="text-right">&euro; {{ parsePrice($transfer->price) }}</td>
                    </tr>
                @empty
                @endforelse
                <tr>
                    <td class="text-left pt-3" colspan="5"><b>TOTAL</b></td>
                    @foreach ($tax['inclusives']['taxes'] as $inc_tax)
                        <td class="text-right">
                            @if ($totals['tax-'. $inc_tax->id] > 0)
                                <b>&euro; {{ parsePrice($totals['tax-'. $inc_tax->id]) }}</b>
                            @endif
                        </td>
                    @endforeach
                    <td class="text-right pt-3" style="border-right: 0;"><b>&euro; {{ parsePrice($booking->total_price) }}</b></td>
                </tr>
                @if($booking->discounts)
                    @foreach($booking->discounts as $disc)
                        <tr>
                            <td class="text-left border-0 py-1" colspan="{{ $colspan_totals }}">
                                <b>Discount</b>
                                - {{ $disc->name }}
                                ({!! $disc->type == 'Percent' ? $disc->value .'%' : '&euro;'. $booking->parsePrice($disc->value) !!} {{ $disc->apply_to }})
                            </td>
                            <td class="text-right border-0 py-1" style="border-right: 0;">
                                @if($disc->type == 'Percent')
                                    @if($disc->apply_to == 'ALL')
                                        &ndash; &euro; {{ parsePrice(round($booking->total_price * ($disc->value / 100), 2)) }}
                                    @else
                                        &ndash; &euro; {{ parsePrice(round($booking->subtotal * ($disc->value / 100), 2)) }}
                                    @endif
                                @else
                                    &ndash; &euro; {{ parsePrice(round($disc->value)) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                @if($tax['exclusives']['total'] > 0)
                    @foreach ($tax['exclusives']['taxes'] as $exc_tax)
                        @php
                            $exc_amount = TaxService::calculateExclusiveTax($booking->subtotal_with_discount, $exc_tax->rate, $exc_tax->type);
                        @endphp
                        <tr>
                            <td class="text-left border-0 py-1" colspan="{{ $colspan_totals }}"><b>{{ $exc_tax->rate }}% {{ $exc_tax->name }}</b></td>
                            <td class="text-right pt-3" style="border-right: 0;">&euro; {{ parsePrice($exc_amount) }}</td>
                        </tr>
                    @endforeach
                @endif
                @if(!is_null($booking->payment->methods) && !in_array($booking->payment->methods, ['cash', 'banktransfer']))
                    <tr>
                        <td class="text-left border-0 py-1 text-danger" colspan="{{ $colspan_totals }}"><b>PAYMENT PROCESSING FEE</b></td>
                        <td class="text-right border-0 py-1 text-danger" style="border-right: 0;"><b>&euro; {{ parsePrice($booking->payment->processing_fee) }}</b></td>
                    </tr>
                @endif
                @if(($booking->discounts && count($booking->discounts)) || ($tax['exclusives']['total'] > 0))
                    <tr>
                        <td class="text-left border-0 py-1 text-danger" colspan="{{ $colspan_totals }}"><b>GRAND TOTAL</b></td>
                        <td class="text-right border-0 py-1 text-danger" style="border-right: 0;">
                            @if (!is_null($booking->payment->methods))
                                <b>&euro; {{ parsePrice($booking->grand_total + $booking->payment->processing_fee) }}</b>
                            @else
                                <b>&euro; {{ parsePrice($booking->grand_total) }}</b>
                            @endif
                        </td>
                    </tr>
                @endif
            @endif
            @if (!request()->has('CANCELLATION'))
                @php
                    $total_paid = 0;
                @endphp
                @if($booking->payment->records->count() > 0)
                    @if (!$is_final)
                        @foreach($booking->payment->records->take($index+1) as $record)
                            @php
                            $total_paid += $record->amount;
                            @endphp
                            <tr>
                                <td class="border-0 py-1 text-danger text-left" colspan="{{ $colspan_totals }}">
                                    <b>Payment Received</b>
                                    ({{ $record->created_at->format('d.m.Y') }})
                                    Down Payment
                                </td>
                                <td class="text-right border-0 py-1 text-danger" style="border-right: 0;">
                                    <b>&ndash; &euro; {{ parsePrice($record->amount) }}</b>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        @foreach($booking->payment->records as $record)
                            @if($record->amount > 0 && $record->verified_at)
                                @php
                                $total_paid += $record->amount;
                                @endphp
                                <tr>
                                    <td class="border-0 py-1 text-danger text-left" colspan="{{ $colspan_totals }}">
                                        <b>Payment Received</b>
                                        ({{$record->created_at->format('d.m.Y')}})
                                        @if (!$is_final && $total_paid < $booking->grand_total)
                                            Down Payment
                                        @elseif (!$is_final && $total_paid >= $booking->grand_total)
                                        @else
                                        @endif
                                    </td>
                                    <td class="text-right border-0 py-1 text-danger" style="border-right: 0;">
                                        <b>&ndash; &euro; {{floatVal(round($booking->parsePrice($record->amount + $booking->payment->processing_fee), 2))}}</b>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endif
            @endif
            @if (request()->has('CANCELLATION') && $booking->has('cancellation'))
                <tr>
                    <td class="border-0 py-1 text-danger" colspan="{{ $colspan_totals }}">
                        Cancellation of booking <b>#{{ $booking->ref }}</b> and invoice <b>#{{ $booking->payment->invoice ? strtoupper($booking->payment->invoice) : 'CCL-'. $booking->ref }}</b>
                    </td>
                    <td class="text-right border-0 py-1 text-danger" style="border-right: 0;">
                        @if (!is_null($booking->payment->methods))
                            <b>&ndash; &euro; {{floatVal($booking->parsePrice($booking->grand_total + $booking->payment->processing_fee))}}</b>
                        @else
                            <b>&ndash; &euro; {{floatVal($booking->parsePrice($booking->grand_total))}}</b>
                        @endif
                    </td>
                </tr>
            @endif
            @if (!request()->has('CANCELLATION'))
                <tr>
                    <td class="text-left border-0 py-1 text-danger" colspan="{{ $colspan_totals }}" style="border-bottom: 0;"><b>TOTAL BALANCE TO PAY</b></td>
                    <td class="text-right border-0 py-1 text-danger" style="border-right: 0; border-bottom: 0">
                        @if ($is_final)
                            <b>&euro; {{ parsePrice($booking->grand_total - $booking->payment->total_paid) }}</b>
                        @else
                            <b>&euro; {{ parsePrice($booking->grand_total - $total_paid) }}</b>
                        @endif
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <br />

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td valign="top" width="50%">
                <p>
                    @if($profile->contact_person)
                        <b>{{$profile->contact_person}}</b><br />
                    @endif
                    @if($profile->address)
                        {!! nl2br($profile->address) !!}<br />
                    @endif
                    @if($profile->iban)
                        IBAN: <b>{{$profile->iban}}</b><br />
                    @endif
                    @if($profile->vat_id)
                        VAT ID: <b>{{$profile->vat_id}}</b><br />
                    @endif
                    @if($profile->commercial_register_number)
                        Register Number: <b>{{$profile->commercial_register_number}}</b><br />
                    @endif
                    @if($profile->district_court)
                        Register court: <b>{{$profile->district_court}}</b>
                    @endif
                </p>
            </td>
            <td class="text-right" valign="top" width="100%">
                @if($tax['inclusives']['total'])
                    @if (!$is_final)
                        @php
                            $diff = TaxService::calculatePercentageDifference($booking->grand_total, $booking->payment->records->get($index)->amount);
                            $totals = collect($totals)->map(fn ($total) => $total * $diff / 100);
                            $total_vat = collect($totals)->sum();
                        @endphp
                        Payment <b>&euro; {{ parsePrice($booking->payment->records->get($index)->amount) }}</b>
                        contains <b>&euro; {{ parsePrice($total_vat) }}</b> VAT
                        ({!! collect($tax['inclusives']['taxes'])->map(fn ($inc_tax) => '<b>&euro; '. round($totals['tax-'. $inc_tax->id], 2) .'</b> '. number_format($inc_tax->rate, 0) .'%')->implode(', ') !!})
                    @else
                        @php
                            $total_vat = collect($totals)->sum();
                        @endphp
                        Price contains <b>&euro; {{ $total_vat }}</b> VAT
                        ({!! collect($tax['inclusives']['taxes'])->map(fn ($inc_tax) => '<b>&euro; '. parsePrice($totals['tax-'. $inc_tax->id]) .'</b> '. number_format($inc_tax->rate, 0) .'%')->implode(', ') !!})
                    @endif
                @endif
            </td>
        </tr>
    </table>

    <div class="absolute" style="bottom: 0; left: 0; right: 0;">
        <b>{{$booking->ref}}</b> {{$booking->location->name}} - {{ request()->has('CANCELLATION') ? 'Invoice for cancellation' : 'Booking Confirmation' }}
    </div>

</body>

</html>
