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
                                    <th width="40%" style="border-top: 1px solid #eee; text-align: left; font-weight: bold; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Session</th>
                                    <th width="32%" style="border-top: 1px solid #eee; text-align: left; font-weight: bold; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Date</th>
                                    <th width="12%" style="border-top: 1px solid #eee; text-align: right; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Subtotal</th>
                                </tr>
                                @foreach($booking->sessions as $r)
                                    <tr>
                                        <td style="border-top: 1px solid #eee; text-align: left; padding: 6px 12px;">
                                            <b>{{ $r->session->category->short_name }} {{ $r->session->name }}</b>
                                        </td>
                                        <td style="border-top: 1px solid #eee; text-align: left; padding: 6px 12px;">
                                            {{ $r->date->format('D, d M y') }}, {{ $r->schedule->start_formatted }} - {{ $r->schedule->end_formatted }}
                                        </td>
                                        <td style="border-top: 1px solid #eee; text-align: right; padding: 6px 12px;"><b>&euro; {{ $r->price }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="border-top: 1px solid #eee; text-align: left; padding: 6px 12px;">
                                            {{ $r->full_name }} ({{ $r->email }})
                                        </td>
                                    </tr>
                                @endforeach
                                @if($booking->addons_count)
                                    @foreach($booking->addons as $addon)
                                        <tr>
                                            <td style="border-top: 1px solid #eee; text-align: left; padding: 6px 12px;" colspan="2">
                                                {{$addon->addon->name}} x {{intVal($addon->amount)}}
                                            </td>
                                            <td style="border-top: 1px solid #eee; text-align: right; padding: 6px 12px;">
                                                <b>&euro; {{$addon->price}}</b>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <td colspan="2" style="font-weight: bold; border-top: 1px solid #ddd; text-align: right; padding: 12px 12px 3px 12px;">SUBTOTAL</td>
                                    <td style="font-weight: bold; border-top: 1px solid #ddd; text-align: right; padding: 12px 12px 3px 12px;">
                                        <b>&euro; {{number_format($booking->total_price, 2)}}</b>
                                    </td>
                                </tr>
                                @if ($booking->discount_value > 0 && $booking->class_multi_passes_id && !$booking->class_multi_pass_payment_id)
                                    <tr>
                                        <td colspan="2" style="font-weight: bold; text-align: right; padding: 3px 12px;">VOUCHER ({{ $booking->pass->code }})</td>
                                        <td style="font-weight: bold; text-align: right; padding: 3px 12px;">
                                            <b>- &euro; {{number_format($booking->discount_value, 2)}}</b>
                                        </td>
                                    </tr>
                                @endif
                                @if ($booking->discount_value > 0 && $booking->class_multi_passes_id && $booking->class_multi_pass_payment_id)
                                    <tr>
                                        <td colspan="2" style="font-weight: bold; text-align: right; padding: 3px 12px;">MULTI PASS (CREDIT)</td>
                                        <td style="font-weight: bold; text-align: right; padding: 3px 12px;">
                                            <b>- &euro; {{number_format($booking->discount_value, 2)}}</b>
                                        </td>
                                    </tr>
                                @endif
                                @if($booking->location->cultural_tax && $booking->location->cultural_tax > 0)
                                    <tr>
                                        <td colspan="2" style="text-align: right; padding: 3px 12px; color: #222">
                                            CULTURAL TAX
                                            {{$booking->location->cultural_tax}}%
                                        </td>
                                        <td style="text-align: right; padding: 3px 12px; color: #222">
                                            &euro; {{($booking->room_tax)}}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="2" style="font-weight: bold; text-align: right; padding: 3px 12px; color: #222">GRAND TOTAL*</td>
                                    <td style="font-weight: bold; text-align: right; padding: 3px 12px; color: #222">
                                        <b>&euro; {{(number_format($booking->grand_total, 2))}}</b>
                                    </td>
                                </tr>
                                @if($booking->payment->open_balance > 0)
                                    <tr>
                                        <td colspan="2" style="font-weight: bold; text-align: right; padding: 3px 12px; color: #fd5b60">OPEN BALANCE</td>
                                        <td style="font-weight: bold; text-align: right; padding: 3px 12px; color: #fd5b60">
                                            <b>&euro; {{(number_format($booking->payment->open_balance, 2))}}</b>
                                        </td>
                                    </tr>
                                @endif
                                
                            </table>
                            @if($booking->location->goods_tax && $booking->grand_total > 0)
                                <div style="font-size: .9em; text-align: right">
                                    Price contains <b>&euro; {{$tax_info['vat']}}</b> VAT (<b>&euro; {{number_format($taxes['goods_tax'], 2)}}</b> {{$taxes['goods_tax_percent']}}%)
                                </div>
                            @endif
                            <br />
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="line-height: 21px;">
                                <tr style="background-color: #fff">
                                    <th width="50%" style="text-align: left; font-weight: bold; text-transform: uppercase; padding-bottom: 10px;">Booker Details</th>
                                    <th width="50%" style="text-align: left; font-weight: bold; text-transform: uppercase; padding-bottom: 10px;">Destination Details</th>
                                </tr>
                                <tr style="font-size: 13px;">
                                    <td style="text-align: left; padding: 8px 0; vertical-align: top">
                                        <b style="text-transform: uppercase;">{{$booking->guest->details->full_name}}</b><br />
                                        <a href="mailto:{{$booking->guest->details->email}}" title="" style="text-decoration: underline dotted; font-weight: bold;">{{$booking->guest->details->email}}</a><br />
                                        {!! $booking->guest->details->phone != '' ? 'Phone: '. $booking->guest->details->phone .'<br />' : '' !!}
                                        {!! $booking->guest->details->street != '' ? $booking->guest->details->street .'<br />' : '' !!}
                                        {!! $booking->guest->details->country != '' ? $booking->guest->details->country .', ' : '' !!}

                                        @if($booking->notes)
                                            <br /><br />
                                            <b>REQUEST / COMMENTS</b><br />
                                            {{$booking->notes}}
                                        @endif
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
