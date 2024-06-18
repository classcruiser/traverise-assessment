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
                                        <th width="60%" style="border-top: 1px solid #eee; text-align: left; font-weight: bold; padding: 10px 0; text-transform: uppercase; font-size: 12px;">Room / Pack.</th>
                                        <th width="10%" style="border-top: 1px solid #eee; text-align: center; font-weight: bold; padding: 10px 0; text-transform: uppercase; font-size: 12px;">QTY</th>
                                        <th width="30%" style="border-top: 1px solid #eee; text-align: right; padding: 10px 0; text-transform: uppercase; font-size: 12px;">Total</th>
                                    </tr>
                                    @foreach($booking->rooms as $r)
                                        <tr>
                                            <td style="border-top: 1px solid #eee; text-align: left; padding: 6px 0;" colspan="2">
                                                <b style="color: #fd5b60">{{$r->room->name}} {!! $r->is_private ? '(Private)' : '' !!}</b>
                                                <br />
                                                Bed: {{$r->bed_type}}<br />
                                                Stay: {{date('d.m.Y', strtotime($r->from))}} - {{date('d.m.Y', strtotime($r->to))}}<br />
                                                ({{$r->days}} days / {{$r->nights}} nights)
                                            </td>
                                            <td style="border-top: 1px solid #eee; text-align: right; padding: 6px 0;"><b>&euro; {{floatVal(($r->price - $r->duration_discount))}}</b></td>
                                        </tr>
                                        @if($r->addons->count() > 0)
                                            @foreach($r->addons as $addon)
                                                <tr>
                                                    <td style="border-top: 1px solid #eee; text-align: left; padding: 6px 0;">
                                                        &rsaquo; {{$addon->details->name}}
                                                        {{$addon->details->rate_type == 'Day' ? '('. $addon->guests .' '. \Illuminate\Support\Str::plural($addon->details->unit_name, $addon->guests) .')' : ''}}
                                                        {{$addon->info}}
                                                    </td>
                                                    <td style="border-top: 1px solid #eee; text-align: center; padding: 6px 0;">
                                                        {{$addon->details->rate_type == 'Day' ? $addon->amount .' '. \Illuminate\Support\Str::plural('day', $addon->amount) : ''}}
                                                    </td>
                                                    <td style="border-top: 1px solid #eee; text-align: right; padding: 6px 0;">
                                                        <b>&euro; {{$addon->price}}</b>
                                                    </td>
                                                </tr>
    
                                                @if($addon->details?->questionnaire && $addon->questionnaire_answers && is_array($addon->questionnaire_answers))
                                                    <tr>
                                                        <td colspan="3" style="border-top: 1px solid #eee; text-align: left; padding: 6px 0;" >
                                                            &rsaquo;
                                                            {{ $addon->details->questionnaire->name }} - <b>{{ implode(', ', $addon->questionnaire_answers)}}</b>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
    
                                    @if($booking->transfers->count() > 0)
                                        @foreach($booking->transfers as $transfer)
                                            <tr>
                                                <td style="border-top: 1px solid #eee; text-align: left; padding: 6px 0;">
                                                    &rsaquo; {{$transfer->details->name}}
                                                    {!! $transfer->flight_detail !!}
                                                </td>
                                                <td style="border-top: 1px solid #eee; text-align: center; padding: 6px 0;">
                                                    {{$transfer->guests}}
                                                </td>
                                                <td style="border-top: 1px solid #eee; text-align: right; padding: 6px 0;"><b>&euro; {{$transfer->price}}</b></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr>
                                        <td colspan="2" style="font-weight: bold; border-top: 1px solid #ddd; text-align: right; padding: 12px 12px 3px 12px;">SUBTOTAL</td>
                                        <td style="font-weight: bold; border-top: 1px solid #ddd; text-align: right; padding: 12px 12px 3px 12px;">
                                            <b>&euro; {{number_format($booking->total_price, 2)}}</b>
                                        </td>
                                    </tr>
                                    @if($booking->discounts)
                                        @foreach($booking->discounts as $disc)
                                            <tr>
                                                <td colspan="2" style="font-weight: bold; text-align: right; padding: 3px 12px;">DISCOUNT</td>
                                                <td style="font-weight: bold; text-align: right; padding: 3px 12px;">
                                                    @if($disc->type == 'Percent')
                                                        @if($disc->apply_to == 'ALL')
                                                            <b>- &euro; {{(number_format($booking->total_price * ($disc->value / 100), 2))}}</b>
                                                        @else
                                                            <b>- &euro; {{(number_format($booking->subtotal * ($disc->value / 100), 2))}}</b>
                                                        @endif
                                                    @else
                                                        <b>- &euro; {{(number_format($disc->value, 2))}}</b>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    @if($booking->location->cultural_tax && $booking->location->cultural_tax > 0)
                                        <tr>
                                            <td colspan="2" style="text-align: right; padding: 3px 12px; color: #222">
                                                CULTURAL TAX
                                                {{$booking->location->cultural_tax}}%
                                            </td>
                                            <td style="text-align: right; padding: 3px 12px; color: #222">
                                                &euro; {{$booking->parsePrice($booking->room_tax)}}
                                            </td>
                                        </tr>
                                    @endif
                                    @if($booking->payment->processing_fee > 0)
                                        <tr>
                                            <td colspan="2" style="font-weight: bold; text-align: right; padding: 3px 12px;">
                                                PAYMENT PROCESSING FEE
                                            </td>
                                            <td style="font-weight: bold; text-align: right; padding: 3px 12px;">
                                                &euro; {{$booking->parsePrice($booking->payment->processing_fee)}}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="2" style="font-weight: bold; text-align: right; padding: 3px 12px; color: #222">GRAND TOTAL*</td>
                                        <td style="font-weight: bold; text-align: right; padding: 3px 12px; color: #222">
                                            <b>&euro; {{(number_format($booking->grand_total + $booking->payment->processing_fee, 2))}}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="font-weight: bold; text-align: right; padding: 3px 12px">TOTAL PAID</td>
                                        <td style="font-weight: bold; text-align: right; padding: 3px 12px;">
                                            &euro; {{(number_format($payment->total_paid + $payment->processing_fee, 2))}}
                                        </td>
                                    </tr>
                                    @if($booking->payment->records()->count() <= 0)
                                        <tr>
                                            <td colspan="2" style="font-weight: bold; text-align: right; padding: 3px 12px; color: #fd5b60">TOTAL TO PAY</td>
                                            <td style="font-weight: bold; text-align: right; padding: 3px 12px; color: #fd5b60">
                                                <b>&euro; {{(number_format($booking->grand_total + $booking->payment->processing_fee, 2))}}</b>
                                            </td>
                                        </tr>
                                    @else
                                        @if($booking->payment->open_balance > 0)
                                            <tr>
                                                <td colspan="2" style="font-weight: bold; text-align: right; padding: 3px 12px; color: #fd5b60">OPEN BALANCE</td>
                                                <td style="font-weight: bold; text-align: right; padding: 3px 12px; color: #fd5b60">
                                                    <b>&euro; {{(number_format($booking->payment->open_balance, 2))}}</b>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                    @if($booking->status != 'DRAFT' && $booking->payment->status == 'DUE' && $booking->location->enable_deposit)
                                        <tr>
                                            <td colspan="2" style="font-weight: bold; text-align: right; padding: 3px 12px;"><b>DEPOSIT (DUE {{$booking->deposit_expiry->format('d.m.Y H:i')}})</b></td>
                                            <td style="font-weight: bold; text-align: right; padding: 3px 12px;"><b>&euro; {{$booking->deposit_amount}}</b></td>
                                        </tr>
                                    @endif
    
                                </table>

                                @if($booking->location->hotel_tax && $booking->location->goods_tax)
                                    <div style="text-align: right; margin-top: 1em !important; margin-right: 1em">{!! $tax_info !!}</div>
                                @endif

                            </div>

                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr><!-- end tr -->
@endsection