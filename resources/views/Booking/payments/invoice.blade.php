@extends('Booking.main')

@section('content')
    <div class="page-content pt-4">
        <div class="content-wrapper container">
            <div class="content">
                <div class="row justify-content-center">
                    <div class="col-sm-10">
                        <div class="card">
                            <div class="card-body py-4 px-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h2 class="mb-0">{{strtoupper($payment->invoice)}}</h2>

                                    @if($booking->payment->status == 'COMPLETED')
                                        <h3 class="mb-0 text-success">COMPLETED</h3>
                                    @endif
                                </div>

                                <div class="mb-3 d-flex justify-content-between align-items-end">
                                    <div>
                                        <b>{{$booking->guest->details->full_name}}</b><br />
                                        {{$booking->guest->details->street}}, {{$booking->guest->details->city}} {{$booking->guest->details->zip}}
                                        <br />
                                        {{$booking->guest->details->country}}
                                        <br />
                                        <a href="#" title="" class="text-danger"><b>{{$booking->guest->details->email}}</b></a>
                                    </div>
                                    <div>
                                        Booking Ref: #<b>{{$booking->ref}}</b>
                                    </div>
                                </div>

                                <table class="table table-xs">
                                    <thead>
                                        <tr class="alpha-grey border-top-1 border-alpha-grey border-bottom-1">
                                            <th class="text-uppercase p-3">Product/Package</th>
                                            <th class="text-uppercase p-3">Bed Type</th>
                                            <th class="text-uppercase p-3">Check In - Check Out</th>
                                            <th class="text-uppercase p-3">Duration</th>
                                            <th class="text-uppercase p-3 text-left">Unit</th>
                                            <th class="text-uppercase p-3 text-right">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($booking->rooms_count)
                                            @foreach($booking->rooms as $r)
                                                <tr>
                                                    <td>
                                                        <b class="text-danger">{{$r->subroom->name}}</b> {!! $r->is_private ? '(Private)' : '' !!}
                                                    </td>
                                                    <td>{{$r->bed_type}}</td>
                                                    <td>{{date('d.m.Y', strtotime($r->from))}} - {{date('d.m.Y', strtotime($r->to))}}</td>
                                                    <td>{{$r->days}} days / {{$r->nights}} nights</td>
                                                    <td class="text-center">{{$r->guest}} {{Str::plural('guest', $r->guest)}}</td>
                                                    <td class="text-right"><b>&euro; {{number_format($r->price, 2)}}</td>
                                                </tr>
                                                @if($r->discounts())
                                                    @foreach($r->discounts as $offer)
                                                        <tr>
                                                            <td colspan="5">
                                                                &rsaquo; Special Offer:
                                                                {{$offer->offer->name}} ({!! $offer->offer->discount_type == 'Percent' ? $offer->offer->discount_value .'%' : '&euro;'. $offer->offer->discount_value !!})
                                                            </td>
                                                            <td class="text-right"><b>- &euro; {{number_format($offer->discount_value, 2)}}</b></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                @if($r->addons->count() > 0)
                                                    @foreach($r->addons as $addon)
                                                        <tr>
                                                            <td colspan="3">&rsaquo; {{$addon->details->name}}</td>
                                                            <td class="text-left">{{$addon->details->is_flexible ? $addon->amount .' days' : ''}}</td>
                                                            <td class="text-left">{{$addon->guests}} {{$addon->details->unit_name}}</td>
                                                            <td class="text-right"><b>&euro; {{number_format($addon->price, 2)}}</b></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td class="alpha-grey p-0" colspan="7" style="height: 7px;"></td>
                                            </tr>
                                            @if($booking->transfers->count() > 0)
                                                @foreach($booking->transfers as $transfer)
                                                    <tr>
                                                        <td colspan="4">
                                                            &rsaquo; {{$transfer->details->name}}
                                                            {!! $transfer->flight_detail !!}
                                                        </td>
                                                        <td class="text-center">{{$booking->total_guests}}</td>
                                                        <td class="text-right"><b>&euro; {{$transfer->price}}</b></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            <tr>
                                                <td class="text-right pt-3" colspan="5"><b>SUBTOTAL</b></td>
                                                <td class="text-right pt-3"><b>&euro;{{(number_format($booking->total_price, 2))}}</b></td>
                                            </tr>
                                            @if($booking->discounts)
                                                @foreach($booking->discounts as $disc)
                                                    <tr>
                                                        <td class="text-right border-0 py-1" colspan="5"><b>DISCOUNT</b></td>
                                                        <td class="text-right border-0 py-1">
                                                            @if($disc->type == 'Percent')
                                                                @if($disc->apply_to == 'ALL')
                                                                    <b>- &euro;{{(number_format($booking->total_price * ($disc->value / 100), 2))}}</b>
                                                                @else
                                                                    <b>- &euro;{{(number_format($booking->subtotal * ($disc->value / 100), 2))}}</b>
                                                                @endif
                                                            @else
                                                                <b>- &euro;{{(number_format($disc->value))}}</b>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            @if ($tax['exclusives']['total'] && count($tax['exclusives']['taxes']))
                                                @foreach ($tax['exclusives']['taxes'] as $tax)
                                                    @php
                                                        $ext_amount = \App\Services\Booking\TaxService::calculateExclusiveTax($booking->subtotal_with_discount, $tax->rate, $tax->type);
                                                    @endphp
                                                    <tr>
                                                        <td class="text-right border-0 py-1" colspan="5"><b>{{ strtoupper($tax->name) }} ({{ number_format($tax->rate, 0) }}%)</b></td>
                                                        <td class="text-right border-0 py-1">
                                                            <b>&euro;{{ parsePrice($ext_amount) }}</b>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            @if(in_array($booking->payment->methods, ['stripe', 'paypal']))
                                                <tr>
                                                    <td class="text-right border-0 py-1" colspan="5"><b>PAYMENT PROCESSING FEE</b></td>
                                                    <td class="text-right border-0 py-1">
                                                        <b>&euro;{{(number_format($payment->processing_fee, 2))}}</b>
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td class="text-right border-0 py-1 text-danger" colspan="5"><b>TOTAL</b></td>
                                                <td class="text-right border-0 py-1 text-danger"><b>&euro; {{(number_format($booking->grand_total + $payment->processing_fee, 2))}}</b></td>
                                            </tr>
                                            @if($booking->payment->total_paid > 0)
                                                <tr>
                                                    <td class="text-right border-0 py-1" colspan="5"><b>PAID</b></td>
                                                    <td class="text-right border-0 py-1"><b>&euro; {{(number_format($total_paid, 2))}}</b></td>
                                                </tr>
                                            @endif
                                        @endif
                                    </tbody>
                                </table>
                                @if($booking->location->goods_tax && $booking->location->hotel_tax)
                                    <div style="text-align: right" class="mt-2 pr-3">{!! $tax_info !!}</div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{asset('js/payment.js')}}"></script>
@endsection
