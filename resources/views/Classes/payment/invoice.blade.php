@extends('Classes.app')

@section('content')
    <div class="bg-neutral-100 min-h-screen">
        <div class="mx-auto px-3 py-10 md:w-2/3">
            <div class="border border-neutral-300 rounded bg-white p-8">
                <div class="mb-7 flex justify-between items-center text-2xl">
                    <h2>{{strtoupper($payment->invoice)}}</h2>
                    @if ($payment->status === 'COMPLETED')
                        <h3 class="mb-0 text-success">COMPLETED</h3>
                    @endif
                </div>
                <div class="mb-5 flex justify-between items-end text-sm">
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
                <table class="w-full font-extralight text-sm">
                    <thead>
                        <tr class="bg-neutral-50 border-y border-neutral-100">
                            <th class="uppercase px-1 py-3">Class</th>
                            <th class="uppercase px-1 py-3">Date</th>
                            <th class="uppercase px-1 py-3">Instructor</th>
                            <th class="uppercase px-1 py-3">Guest</th>
                            <th class="uppercase px-1 py-3">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($booking->sessions as $session)
                            <tr class="border-b border-neutral-100">
                                <td class="p-2 text-danger-300">
                                    <b>{{ $session->session->category->short_name }} {{ $session->session->name }}</b>
                                </td>
                                <td class="p-2">{{ $session->date->format('l, d M y') }}, {{ $session->schedule->start_formatted }} - {{ $session->schedule->end_formatted }}</td>
                                <td class="p-2">{{ $session->instructor?->name ?? '-' }}</td>
                                <td class="p-2">{{ $session->full_name }}</td>
                                <td class="p-2 text-right font-bold">&euro; {{ $session->price }}</td>
                            </tr>
                        @empty
                            <tr class="border-b border-neutral-100">
                                <td colspan="5" class="p-2 text-center">No class added</td>
                            </tr>
                        @endforelse
                        @forelse($booking->addons as $addon)
                            <tr class="border-b border-neutral-100">
                                <td colspan="2" class="p-2">
                                    <i class="fa fa-gift fa-fw mr-1 text-danger-300 tippy" data-tippy-content="Extra / Addon"></i> {{$addon->addon->name}}
                                </td>
                                <td class="text-left p-2">
                                    @if($addon->addon->rate_type == 'Day')
                                        {{intVal($addon->amount)}} {{$addon->addon->unit_name}}
                                    @endif
                                </td>
                                <td class="text-center p-2">
                                    {{$addon->amount}} <i class="far fa-user"></i>
                                </td>
                                <td class="text-right p-2 font-bold">&euro; {{ $addon->price }}</td>
                            </tr>
                        @empty
                            <tr class="border-b border-neutral-100">
                                <td colspan="5" class="text-center p-2">No addons added</td>
                            </tr>
                        @endforelse
                        <tr class="border-b border-neutral-100">
                            <td colspan="5" class="bg-neutral-50 px-2"></td>
                        </tr>
                        <tr>
                            <td class="text-right font-bold px-2 pb-1 pt-3" colspan="4">SUBTOTAL</td>
                            <td class="text-right font-bold px-2 pb-1 pt-3">&euro; {{(number_format($booking->total_price, 2))}}</td>
                        </tr>
                        @if ($taxes['cultural_tax_percent'] && $taxes['cultural_tax_percent'] > 0)
                            <tr>
                                <td class="text-right font-bold px-2 py-1" colspan="4">{{$taxes['cultural_tax_percent']}}% CULTURAL TAX</td>
                                <td class="text-right font-bold px-2 py-1">&euro;{{ $booking->room_tax }}</td>
                            </tr>
                        @endif

                        @if (($taxes['hotel_tax_percent'] && $taxes['hotel_tax_percent'] > 0) && ($taxes['goods_tax_percent'] && $taxes['goods_tax_percent'] > 0))
                            <tr>
                                <td class="text-right font-bold px-2 py-1" colspan="4"><b>VAT</b></td>
                                <td class="text-right font-bold px-2 py-1">{{$taxes['hotel_tax_percent']}}% &euro;{{number_format($taxes['hotel_tax'], 2)}}</td>
                            </tr>
                            <tr>
                                <td class="text-right font-bold px-2 py-1" colspan="4">&nbsp;</td>
                                <td class="text-right font-bold px-2 py-1">{{$taxes['goods_tax_percent']}}% &euro;{{number_format($taxes['goods_tax'], 2)}}</td>
                            </tr>
                        @endif
                        @if(in_array($booking->payment->methods, ['stripe']))
                            <tr>
                                <td class="text-right font-bold px-2 py-1" colspan="4">PAYMENT PROCESSING FEE</td>
                                <td class="text-right font-bold px-2 py-1">&euro; {{(number_format($payment->processing_fee, 2))}}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-right font-bold px-2 py-1" colspan="4"><b>GRAND TOTAL</b></td>
                            <td class="text-right font-bold px-2 py-1">&euro;{{number_format($booking->grand_total, 2)}}</td>
                        </tr>
                        @if($booking->payment->total_paid > 0)
                            <tr>
                                <td class="text-right font-bold px-2 py-1 text-blue" colspan="4">TOTAL PAID</td>
                                <td class="text-right font-bold px-2 py-1 text-blue">&euro;{{number_format($booking->payment->total_paid, 2)}}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
