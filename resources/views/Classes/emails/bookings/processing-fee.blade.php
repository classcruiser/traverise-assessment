@extends('Booking.emails.layout')

@section('body')
    <tr>
        <td valign="middle" class="hero bg_white" style="padding: 2em 0 2em 0;">
            <table>
                <tr>
                    <td>
                        <div class="text" style="padding: 0 2.5em; text-align: left; word-break: break-word; font-size: .9em">
                            <p>Dear <b>{{ $booking->guest->details->full_name }}</b>,</p>

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
                                        <th width="61%" style="border-top: 1px solid #eee; text-align: left; font-weight: bold; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Description</th>
                                        <th width="13%" style="border-top: 1px solid #eee; text-align: center; font-weight: bold; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Qty</th>
                                        <th width="13%" style="border-top: 1px solid #eee; text-align: right; font-weight: bold; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Unit price</th>
                                        <th width="13%" style="border-top: 1px solid #eee; text-align: right; padding: 10px 12px; text-transform: uppercase; font-size: 12px;">Amount</th>
                                    </tr>
                                    <tr>
                                        <td style="border-top: 1px solid #eee; text-align: left; padding: 6px 12px;">
                                            <b style="color: #fd5b60">Payment Processing Fee {{ strtoupper($payment->invoice) }}</b>
                                        </td>
                                        <td style="border-top: 1px solid #eee; text-align: center; padding: 6px 12px;">
                                            1
                                        </td>
                                        <td style="border-top: 1px solid #eee; text-align: right; padding: 6px 12px;">
                                            <b>&euro; {{ ($record->data['charges']['data'][0]['balance_transaction']['fee'] / 100) }}</b>
                                        </td>
                                        <td style="border-top: 1px solid #eee; text-align: right; padding: 6px 12px;">
                                            <b>&euro; {{ ($record->data['charges']['data'][0]['balance_transaction']['fee'] / 100) }}</b>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="3" style="font-weight: bold; border-top: 1px solid #ddd; text-align: right; padding: 12px 12px 3px 12px;">SUBTOTAL</td>
                                        <td style="font-weight: bold; border-top: 1px solid #ddd; text-align: right; padding: 12px 12px 3px 12px;">
                                            <b>&euro; {{ ($record->data['charges']['data'][0]['balance_transaction']['fee'] / 100) }}</b>
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
