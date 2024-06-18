<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>Invoice Booking {{$booking->ref}}</title>
    <style type="text/css">
    body {
        font-family: "Helvetica";
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
        border: 1px solid #999;
        font-size: 8pt;
    }

    table.invoice tr th {
        padding: 5px 10px;
        background-color: #eee;
        font-size: 8pt;
        border-bottom: 1px solid #999;
    }

    table.invoice tr td {
        padding: 5px 10px;
        border-right: 1px solid #999;
        border-bottom: 1px solid #999;
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
                <div style="height: 150px;">
                    @if(file_exists(public_path('tenancy/assets/images/camps/'. ($profile ? $profile->tenant_id : tenant('id')) .'_logo.jpg')))
                        <img src="{{ public_path('tenancy/assets/images/camps/'. ($profile ? $profile->tenant_id : tenant('id')) .'_logo.jpg') }}" alt="{{$profile->title}}" style="width: 150px; display: block;" />
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
                        Invoice Ref: <b>{{strtoupper($booking->payment->invoice)}}</b>
                    @endif
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px 0; text-align: center; font-size: 12pt; font-weight: bold;" colspan="2">
                Invoice &amp; Booking Confirmation
            </td>
        </tr>
        <tr>
            <td valign="top">
                <p style="font-size: 11pt"><b>Booker details:</b></p>
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
                            <img src="{{ public_path('qr-session/'. $booking->ref .'-'. $booking->id .'.png') }}" style="width: 100px; height: 100px; display: block; margin-bottom: 1rem;" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="invoice">
        <thead>
            <tr class="alpha-grey border-top-1 border-alpha-grey border-bottom-1">
                <th class="text-uppercase p-3">Guest</th>
                <th class="text-uppercase p-3">Session</th>
                <th class="text-uppercase p-3">Date</th>
                <th class="text-uppercase p-3 text-center">Quantity</th>
                <th class="text-uppercase p-3 text-right">Price</th>
            </tr>
        </thead>
        <tbody>
            @if($booking->sessions_count)
                @foreach($booking->sessions as $r)
                    <tr>
                        <td><b>{{ $r->full_name }}</b></td>
                        <td>
                            <b class="text-danger">{{ $r->session->category->name .': '. $r->session->name }}</b>
                        </td>
                        <td>{{ date('l, d.m.Y', strtotime($r->date)) }} {{ $r->schedule->start_formatted .' - '. $r->schedule->end_formatted }}</td>
                        <td class="text-center">1</td>
                        <td class="text-right"><b>&euro; {{ $r->price }}</td>
                    </tr>
                    @if($booking->location->duration_discount)
                        <tr>
                            <td colspan="{{!$booking->archived ? 5 : 4}}">&rsaquo; Duration Discount</td>
                            <td class="text-right"><b>- &euro; {{floatVal(($r->duration_discount))}}</b></td>
                        </tr>
                    @endif
                @endforeach
                @if($booking->addons_count > 0)
                    @foreach($booking->addons as $addon)
                        <tr>
                            <td colspan="3">Add-on: {{$addon->addon->name}}</td>
                            <td class="text-center">{{ $addon->amount }} {{ Illuminate\Support\Str::plural($addon->addon->unit_name, $addon->amount)}}</td>
                            <td class="text-right"><b>&euro; {{($addon->price)}}</b></td>
                        </tr>
                    @endforeach
                @endif
                <tr>
                    <td class="text-right pt-3" colspan="4"><b>SUBTOTAL</b></td>
                    <td class="text-right pt-3"><b>&euro; {{floatVal(round(($booking->total_price), 2))}}</b></td>
                </tr>
                @if ($booking->discount_value > 0 && $booking->class_multi_passes_id && !$booking->class_multi_pass_payment_id)
                    <tr>
                        <td class="text-right pt-3" colspan="4"><b>VOUCHER</b> {{ $booking->pass->code }}</td>
                        <td class="text-right pt-3"><b>&ndash; &euro; {{floatVal(round(($booking->discount_value), 2))}}</b></td>
                    </tr>
                @endif
                @if ($booking->discount_value > 0 && $booking->class_multi_passes_id && $booking->class_multi_pass_payment_id)
                    <tr>
                        <td class="text-right pt-3" colspan="4"><b>MULTI PASS</b> ({{ $booking->pass->name }})</td>
                        <td class="text-right pt-3"><b>&ndash; &euro; {{floatVal(round(($booking->discount_value), 2))}}</b></td>
                    </tr>
                @endif
                @if($booking->discounts)
                    @foreach($booking->discounts as $disc)
                        <tr>
                            <td class="text-right border-0 py-1" colspan="4"><b>DISCOUNT</b></td>
                            <td class="text-right border-0 py-1">
                                @if($disc->type == 'Percent')
                                    @if($disc->apply_to == 'ALL')
                                        <b>&ndash; &euro; {{floatVal((round($booking->total_price * ($disc->value / 100), 2)))}}</b>
                                    @else
                                        <b>&ndash; &euro; {{floatVal((round($booking->subtotal * ($disc->value / 100), 2)))}}</b>
                                    @endif
                                @else
                                    <b>&ndash; &euro; {{floatVal((round($disc->value)))}}</b>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr>
                    <td class="text-right border-0 py-1 text-danger" colspan="4"><b>TOTAL</b></td>
                    <td class="text-right border-0 py-1 text-danger">
                        <b>&euro; {{floatVal(($booking->grand_total))}}</b>
                    </td>
                </tr>
            @endif
            @if($booking->payment->records->count() > 0)
                @foreach($booking->payment->records as $record)
                    @if($record->amount > 0 && $record->verified_at)
                        <tr>
                            <td class="border-0 py-1 text-danger" colspan="4">
                                <b>Payment Received</b>
                                ({{$record->created_at->format('d.m.Y')}})
                            </td>
                            <td class="text-right border-0 py-1 text-danger"><b>&ndash; &euro; {{floatVal(round(($record->amount), 2))}}</b></td>
                        </tr>
                    @endif
                @endforeach
            @endif
            <tr>
                <td class="text-right border-0 py-1 text-danger" colspan="4"><b>TOTAL TO PAY</b></td>
                <td class="text-right border-0 py-1 text-danger"><b>&euro; {{floatVal(round(($booking->payment->open_balance), 2))}}</b></td>
            </tr>
        </tbody>
    </table>

    <br />

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="left" valign="top">
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
            <td align="right" valign="top">
                @if($booking->location->goods_tax)
                    Price contains <b>&euro; {{$tax_info['vat']}}</b> VAT
                    (<b>&euro; {{number_format($taxes['goods_tax'], 2)}}</b> {{$taxes['goods_tax_percent']}}%)
                @endif
            </td>
        </tr>
    </table>

    <div class="absolute" style="bottom: 0; left: 0; right: 0;">
        <b>{{$booking->ref}}</b> {{$booking->location->name}} - Booking Confirmation
    </div>

</body>

</html>
