<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>Cancellation Invoice Booking {{$booking->ref}}</title>
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
                <div style="height: 122px;">
                    @if(file_exists(public_path('tenancy/assets/images/camps/'. tenant('id') .'_logo.jpg')))
                        <img src="{{public_path('tenancy/assets/images/camps/'. tenant('id') .'_logo.jpg')}}" alt="{{$profile->title}}" style="width: 200px" />
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
                    Invoice Ref: <b>{{strtoupper($booking->cancellation->ref)}}</b>
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px 0; text-align: center; font-size: 12pt; font-weight: bold;" colspan="2">
                CANCELLATION INVOICE<br />
                {{ strtoupper('Stornorechnung') }}
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
                        <td width="50%" align="right">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="invoice">
        <thead>
            <tr class="alpha-grey border-top-1 border-alpha-grey border-bottom-1">
                <th class="text-uppercase p-3">Product/Package</th>
                <th class="text-uppercase p-3 text-right">Amount</th>
                <th class="text-uppercase p-3 text-right">Price</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><b>Cancellation Fee</b></td>
                <td class="text-right">{{ $booking->cancellation->cancellation_fee }}%</td>
                <td class="text-right">
                    <b>&euro; {{ number_format($cancellation_fee_amount, 2) }}</b>
                </td>
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
                Price contains <b>&euro; {{ number_format($booking->cancellation->calculateTaxAmount($cancellation_fee_amount), 2) }}</b> VAT
                (<b>&euro; {{ number_format($booking->cancellation->calculateTaxAmount($cancellation_fee_amount), 2) }}</b> 19%)
            </td>
        </tr>
    </table>

    <div class="absolute" style="bottom: 0; left: 0; right: 0;">
        <b>{{$booking->cancellation->ref}}</b> - Cancellation Invoice
    </div>

</body>

</html>
