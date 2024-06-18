<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Invoice Booking {{$payment->ref}}</title>
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
                @if(file_exists(public_path('tenancy/assets/images/camps/'. ($profile ? $profile->tenant_id : tenant('id')) .'_logo.jpg')))
                    <img src="{{ public_path('tenancy/assets/images/camps/'. ($profile ? $profile->tenant_id : tenant('id')) .'_logo.jpg') }}" alt="{{$profile->title}}" style="width: 150px; display: block;" />
                @else
                    <h2>{{$profile->title}}</h2>
                @endif
            </div>
        </td>
        <td align="right" valign="top" width="60%">
            <p>
                <span style="font-size: 10pt">Order ref: <b>{{$payment->ref}}</b></span><br/>
                Created at: <b>{{$payment->created_at->format('d.m.Y, H:i')}}</b><br/>
                State: <b>{{$payment->status}}</b><br/>
                @if($payment->invoice)
                    Invoice Ref: <b>{{strtoupper($payment->invoice)}}</b>
                @endif
            </p>
        </td>
    </tr>
    <tr>
        <td style="padding: 20px 0; text-align: center; font-size: 12pt; font-weight: bold;" colspan="2">
            Multi-Pass Invoice
        </td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="invoice">
    <thead>
    <tr class="alpha-grey border-top-1 border-alpha-grey border-bottom-1">
        <th class="text-uppercase p-3">Description</th>
        <th class="text-uppercase p-3">Qty</th>
        <th class="text-uppercase p-3">Unit price</th>
        <th class="text-uppercase p-3 text-center">Amount</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="border-top: 1px solid #eee; text-align: left; padding: 6px 12px;">
            <b>{{ $payment->multiPass->name }}</b>
        </td>
        <td style="border-top: 1px solid #eee; text-align: center; padding: 6px 12px;">
            1
        </td>
        <td style="border-top: 1px solid #eee; text-align: right; padding: 6px 12px;">
            <b>&euro; {{ number_format($payment->total, 2)  }}</b></td>
        <td style="border-top: 1px solid #eee; text-align: right; padding: 6px 12px;">
            <b>&euro; {{ number_format($payment->total, 2)  }}</b></td>
    </tr>

    <tr>
        <td colspan="3"
            style="font-weight: bold; border-top: 1px solid #ddd; text-align: right; padding: 12px 12px 3px 12px;">TOTAL
        </td>
        <td style="font-weight: bold; border-top: 1px solid #ddd; text-align: right; padding: 12px 12px 3px 12px;">
            <b>&euro; {{number_format($payment->total, 2)}}</b>
        </td>
    </tr>
    @if($payment->location->goods_tax && $payment->location->goods_tax > 0)
        <tr>
            <td colspan="3" style="text-align: right; padding: 3px 12px; color: #222">
                GOODS TAX
                {{$payment->location->goods_tax}}%
            </td>
            <td style="text-align: right; padding: 3px 12px; color: #222">
                &euro; {{ number_format($tax,2 ) }}
            </td>
        </tr>
    @endif
    </tbody>
</table>


@if($payment->location->goods_tax)
    <div style="font-size: .9em; text-align: right">
        Price contains <b>&euro; {{number_format($tax, 2)}}</b> VAT
        (<b>&euro; {{number_format($tax, 2)}}</b> {{$payment->location->goods_tax}}%)
    </div>
@endif
<br/>
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="line-height: 21px;">
    <tr style="background-color: #fff">
        <th width="50%" style="text-align: left; font-weight: bold; text-transform: uppercase; padding-bottom: 10px;">
            Purchaser Details
        </th>
        <th width="50%" style="text-align: left; font-weight: bold; text-transform: uppercase; padding-bottom: 10px;">
            Destination Details
        </th>
    </tr>
    <tr style="font-size: 13px;">
        <td style="text-align: left; padding: 8px 0; vertical-align: top">
            <b style="text-transform: uppercase;">{{$payment->guest->full_name}}</b><br/>
            {!! $payment->guest->company != '' ? 'Company: '. $payment->guest->company .'<br />' : '' !!}
            <a href="mailto:{{$payment->guest->email}}" title=""
               style="text-decoration: underline dotted; font-weight: bold;">{{$payment->guest->email}}</a><br/>
            {!! $payment->guest->phone != '' ? 'Phone: '. $payment->guest->phone .'<br />' : '' !!}
            {!! $payment->guest->street != '' ? $payment->guest->street .', '. $payment->guest->city .'<br />' : '' !!}
            {!! $payment->guest->country != '' ? $payment->guest->country .', '. $payment->guest->zip : '' !!}
        </td>
        <td style="text-align: left; padding: 8px 0; vertical-align: top" class="first-paragraph">
            <b style="text-transform: uppercase;">{{$payment->location->name}}</b><br/>
            {!! $payment->location->address !!}<br/>
            {!! $payment->location->phone ? 'Phone: '. $payment->location->phone .'<br />' : '' !!}
            Email: {{$payment->location->contact_email}}
        </td>
    </tr>
</table>


</body>

</html>
