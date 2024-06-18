@extends('Booking.emails.layout')

@section('body')
    <tr>
        <td valign="middle" class="hero bg_white" style="padding: 0 0 2em 0;">
            <table>
                <tr>
                    <td>
                        <div class="text" style="padding: 0 2em; text-align: left; word-break: break-word; font-size: .95em">
                            {!! $template !!}

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 13px; line-height: 21px;">
                                <tr style="background-color: #f7f7f7">
                                    <th width="61%" style="border-top: 1px solid #eee; text-align: left; font-weight: bold; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Description</th>
                                    <th width="13%" style="border-top: 1px solid #eee; text-align: center; font-weight: bold; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Qty</th>
                                    <th width="13%" style="border-top: 1px solid #eee; text-align: right; font-weight: bold; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Unit price</th>
                                    <th width="13%" style="border-top: 1px solid #eee; text-align: right; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Amount</th>
                                </tr>
                                <tr>
                                    <td style="border-top: 1px solid #eee; text-align: left; padding: 6px 12px;">
                                        <b>{{ $booking->multiPass->name }}</b>
                                    </td>
                                    <td style="border-top: 1px solid #eee; text-align: center; padding: 6px 12px;">
                                        1
                                    </td>
                                    <td style="border-top: 1px solid #eee; text-align: right; padding: 6px 12px;"><b>&euro; {{ number_format($booking->total, 2)  }}</b></td>
                                    <td style="border-top: 1px solid #eee; text-align: right; padding: 6px 12px;"><b>&euro; {{ number_format($booking->total, 2)  }}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-weight: bold; border-top: 1px solid #ddd; text-align: right; padding: 12px 12px 3px 12px;">TOTAL</td>
                                    <td style="font-weight: bold; border-top: 1px solid #ddd; text-align: right; padding: 12px 12px 3px 12px;">
                                        <b>&euro; {{number_format($booking->total, 2)}}</b>
                                    </td>
                                </tr>
                                @if($booking->location->goods_tax && $booking->location->goods_tax > 0)
                                    <tr>
                                        <td colspan="3" style="text-align: right; padding: 3px 12px; color: #222">
                                            GOODS TAX
                                            {{$booking->location->goods_tax}}%
                                        </td>
                                        <td style="text-align: right; padding: 3px 12px; color: #222">
                                            &euro; {{ number_format($taxes['goods_tax'],2 ) }}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                            @if($booking->location->goods_tax)
                                <div style="font-size: .9em; text-align: right">
                                    Price contains <b>&euro; {{$tax_info['vat']}}</b> VAT (<b>&euro; {{number_format($taxes['goods_tax'], 2)}}</b> {{$taxes['goods_tax_percent']}}%)
                                </div>
                            @endif
                            <br />
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="line-height: 21px;">
                                <tr style="background-color: #fff">
                                    <th width="50%" style="text-align: left; font-weight: bold; text-transform: uppercase; padding-bottom: 10px;">Purchaser Details</th>
                                    <th width="50%" style="text-align: left; font-weight: bold; text-transform: uppercase; padding-bottom: 10px;">Destination Details</th>
                                </tr>
                                <tr style="font-size: 13px;">
                                    <td style="text-align: left; padding: 8px 0; vertical-align: top">
                                        <b style="text-transform: uppercase;">{{$booking->guest->full_name}}</b><br />
                                        <a href="mailto:{{$booking->guest->email}}" title="" style="text-decoration: underline dotted; font-weight: bold;">{{$booking->guest->email}}</a><br />
                                        {!! $booking->guest->phone != '' ? 'Phone: '. $booking->guest->phone .'<br />' : '' !!}
                                        {!! $booking->guest->street != '' ? $booking->guest->street .'<br />' : '' !!}
                                        {!! $booking->guest->country != '' ? $booking->guest->country .', ' : '' !!}
                                    </td>
                                    <td style="text-align: left; padding: 8px 0; vertical-align: top" class="first-paragraph">
                                        <b style="text-transform: uppercase;">{{$booking->location->name}}</b><br />
                                        {!! $booking->location->address !!}<br />
                                        {!! $booking->location->phone ? 'Phone: '. $booking->location->phone .'<br />' : '' !!}
                                        Email: {{$booking->location->contact_email}}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr><!-- end tr -->
@endsection
