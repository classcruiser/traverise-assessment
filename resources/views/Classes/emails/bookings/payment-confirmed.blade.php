@extends('Booking.emails.layout')

@section('body')
    <tr>
        <td valign="middle" class="hero bg_white" style="padding: 2em 0 2em 0;">
            <table>
                <tr>
                    <td>
                        <div class="text" style="padding: 0 2.5em; text-align: left; word-break: break-word; font-size: .9em">

                            {!! $template !!}

                            <div style="border-top: 1px solid #ddd; padding-top: 1.25em; margin-top: 1.75em;">
                                <table width="100%">
                                    <tr>
                                        <td width="50%" align="left" valign="middle">
                                            <h3 class="heading" style="margin: 0; padding: 0;">{{strtoupper($payment->invoice)}}</h3>
                                        </td>
                                        <td width="50%" align="right" valign="middle">
                                            @if($payment->status == 'COMPLETED')
                                                <h4 style="color: green; margin: 0; padding: 0;">PAID</h4>
                                            @endif
                                        </td>
                                    </tr>
                                </table>

                                <p>
                                    <b>{{$booking->guest->details->full_name}}</b><br />
                                    {{$booking->guest->details->street}}, {{$booking->guest->details->city}} {{$booking->guest->details->zip}}
                                    <br />
                                    {{$booking->guest->details->country}}
                                    <br />
                                    <a href="mailto:{{$booking->guest->details->email}}" title=""><b>{{$booking->guest->details->email}}</b></a>
                                </p>

                                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 13px; line-height: 21px;">
                                    <tr style="background-color: #f7f7f7">
                                        <th width="35%" style="border-top: 1px solid #eee; text-align: left; font-weight: bold; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Class</th>
                                        <th width="32%" style="border-top: 1px solid #eee; text-align: left; font-weight: bold; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Date</th>
                                        <th width="12%" style="border-top: 1px solid #eee; text-align: center; font-weight: bold; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Guest</th>
                                        <th width="15%" style="border-top: 1px solid #eee; text-align: right; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Total</th>
                                    </tr>
                                    @foreach($booking->sessions as $r)
                                        <tr>
                                            <td style="border-top: 1px solid #eee; text-align: left; padding: 6px 12px;">
                                                <b style="color: #fd5b60">{{ $r->session->name }}</b>
                                            </td>
                                            <td style="border-top: 1px solid #eee; text-align: left; padding: 6px 12px;">
                                                {{ $r->date->format('l, d M y') }}, {{ $r->schedule->start_formatted }} - {{ $r->schedule->end_formatted }}
                                            </td>
                                            <td style="border-top: 1px solid #eee; text-align: center; padding: 6px 12px;">
                                                {{ $r->full_name }}
                                            </td>
                                            <td style="border-top: 1px solid #eee; text-align: right; padding: 6px 12px;"><b>&euro; {{floatVal(($r->session->price))}}</b></td>
                                        </tr>
                                    @endforeach
                                    @foreach($booking->addons as $addon)
                                        <tr>
                                            <td style="border-top: 1px solid #eee; text-align: left; padding: 6px 12px;">
                                                &rsaquo; {{$addon->addon->name}}
                                            </td>
                                            <td style="border-top: 1px solid #eee; text-align: left; padding: 6px 12px;">
                                                @if($addon->addon->rate_type == 'Day')
                                                    {{intVal($addon->amount)}} {{$addon->addon->unit_name}}
                                                @endif
                                            </td>
                                            <td style="border-top: 1px solid #eee; text-align: center; padding: 6px 12px;">
                                                {{$addon->amount}} <i class="far fa-user"></i>
                                            </td>
                                            <td style="border-top: 1px solid #eee; text-align: right; padding: 6px 12px;">
                                                <b>&euro; {{$addon->price}}</b>
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="3" style="font-weight: bold; border-top: 1px solid #ddd; text-align: right; padding: 12px 12px 3px 12px;">SUBTOTAL</td>
                                        <td style="font-weight: bold; border-top: 1px solid #ddd; text-align: right; padding: 12px 12px 3px 12px;">
                                            <b>&euro; {{number_format($booking->total_price, 2)}}</b>
                                        </td>
                                    </tr>
                                    @if($payment->methods != 'banktransfer' && $payment->processing_fee > 0)
                                        <tr>
                                            <td colspan="3" style="font-weight: bold; text-align: right; padding: 3px 12px;">
                                                PAYMENT PROCESSING FEE
                                            </td>
                                            <td style="font-weight: bold; text-align: right; padding: 3px 12px;">
                                                &euro; {{ $payment->processing_fee }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3" style="font-weight: bold; text-align: right; padding: 3px 12px; color: #222">GRAND TOTAL*</td>
                                        <td style="font-weight: bold; text-align: right; padding: 3px 12px; color: #222">
                                            <b>&euro; {{(number_format($booking->grand_total + $payment->processing_fee, 2))}}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="font-weight: bold; text-align: right; padding: 3px 12px">TOTAL PAID</td>
                                        <td style="font-weight: bold; text-align: right; padding: 3px 12px;">
                                            &euro; {{(number_format($payment->total_paid + $payment->processing_fee, 2))}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="font-weight: bold; text-align: right; padding: 3px 12px; color: #fd5b60">OPEN BALANCE</td>
                                        <td style="font-weight: bold; text-align: right; padding: 3px 12px; color: #fd5b60">
                                            &euro; {{(number_format($payment->open_balance, 2))}}
                                        </td>
                                    </tr>

                                </table>

                            </div>

                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr><!-- end tr -->
@endsection
