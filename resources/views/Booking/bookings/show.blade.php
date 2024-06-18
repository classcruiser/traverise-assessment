@extends('Booking.app')
@php
    $ext_tax = 0;
@endphp
@section('content')
    <div class="App-bookings">

        @include('Booking.partials.bookings.add-discount')

        @include('Booking.partials.bookings.payment-link-email')

        @include('Booking.partials.bookings.add-transfer')

        @include('Booking.partials.bookings.approve-booking')

        @if($booking->discounts)
            @foreach($booking->discounts as $disc)
                @include('Booking.partials.bookings.edit-discount', ['modal_id' => 'edit-discount-'. $disc->id, 'discount' => $disc])
            @endforeach
        @endif

        @if($booking->transfers_count > 0)
            @foreach($booking->transfers as $transfer)
                @include('Booking.partials.bookings.edit-transfer', ['modal_id' => 'edit-transfer-'. $transfer->id, 'transfer' => $transfer])
            @endforeach
        @endif

        @include('Booking.partials.bookings.payment-record')

        @include('Booking.partials.bookings.cancel-booking')

        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="/dashboard" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
                    <a href="/bookings" class="breadcrumb-item">Bookings</a>
                    <span class="breadcrumb-item active"># {{$booking->ref}}</span>
                </div>

                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>

        <div class="page-content new pt-4">

            @include('Booking.bookings.sidebar')

            <div class="content-wrapper container reset">
                <div class="content">
                    @if($booking->is_blacklisted)
                        <div class="d-inline-block blacklist w-100 text-uppercase mb-3 text-center">
                            <i class="fa fa-ban mr-1"></i>
                            <span class="text-grey-800">This booking contains one or more blacklisted guest</span>
                            <i class="fa fa-ban mr-1"></i>
                        </div>
                    @endif

                    <div class="mb-3 d-flex justify-content-between hide-on-mobile">
                        <div class="left-toolbar">
                            @if(!$is_deleted)
                                @can ('edit booking')
                                    <a href="/bookings/{{$booking->ref}}/new-guest" class="btn btn-sm bg-danger-400"><i class="fa fa-fw fa-user"></i> Add Guest</a>
                                    <button class="btn btn-sm bg-danger-400" data-toggle="modal" data-target="#modal_add_discount"><i class="fa fa-fw fa-dollar-sign"></i> Add Discount</button>
                                    <button class="btn btn-sm bg-danger-400" data-toggle="modal" data-target="#modal_add_transfer"><i class="fa fa-fw fa-plane"></i> Add Transfers</button>
                                @endcan
                            @endif
                        </div>
                        <div class="right-toolbar d-flex justify-content-end align-items-center">
                            @if($booking->status == 'CONFIRMED' && !$is_deleted)
                                <div class="input-group input-group-sm mr-1" style="width: 280px;">
                                    <input type="text" class="form-control" placeholder="Resend booking confirmation to"
                                           v-model="payment.resend_email">
                                    <span class="input-group-append">
                                        <a class="input-group-text bg-danger-400 resend-confirmation" href="javascript:"
                                           v-html="payment.resend_button" :disabled="payment.resend_loading"
                                           @click="resendConfirmation({{$booking->id}})"></a>
                                    </span>
                                </div>
                            @endif
                            @if($booking->status != 'CANCELLED' && $booking->status != 'EXPIRED' && !$is_deleted)
                                <button class="btn bg-danger-400 btn-sm rounded" data-toggle="dropdown">
                                    Manage <i class="fal fa-angle-down ml-1"></i>
                                </button>
                            @endif
                            <div class="dropdown-menu dropdown-menu-sm">
                                @if($booking->status != 'CONFIRMED')
                                    <a href="#" class="dropdown-item" data-toggle="modal"
                                       data-target="#modal_approve_booking" data-ref="{{$booking->ref}}"><i
                                            class="fal fa-fw fa-check mr-1"></i> Approve</a>

                                    @if($booking->status == 'DRAFT')
                                        <a href="javascript:" @click="sendBookingLink('{{$booking->id}}')" class="dropdown-item">
                                            <i class="fal fa-fw fa-money-bill-1-wave mr-1"></i> Send Booking Link
                                        </a>
                                    @endif
                                @else
                                    <a href="{{url('payment/'. $booking->payment->link)}}" class="dropdown-item" target="_blank">
                                        <i class="fal fa-fw fa-money-bill-alt mr-1"></i> Go to payment page
                                    </a>

                                    @can ('edit booking')
                                        <a href="javascript:" class="dropdown-item" data-toggle="modal" data-target="#modal_cancel_booking">
                                            <i class="fal fa-fw fa-times mr-1"></i> Cancel booking
                                        </a>
                                    @endcan
                                @endif
                                @can('delete booking')
                                    <a href="/bookings/{{$booking->ref}}/delete" class="dropdown-item confirm-dialog" data-text="DELETE BOOKING?">
                                        <i class="fa fa-fw fa-trash mr-1"></i> Delete Booking
                                    </a>
                                @endcan
                            </div>

                            @if ($booking->status !== 'CANCELLED')
                                @can('download PDF')
                                    <a href="/bookings/{{$booking->ref}}/pdf-invoice" class="btn btn-sm bg-danger-800 ml-1">
                                        <i class="fa fa-fw fa-file-pdf"></i> {{ $booking->status == 'CANCELLED' ? 'CANCELLATION' : 'INVOICE' }}
                                    </a>
                                @endcan
                            @endif
                        </div>
                    </div>

                    <div class="card booking-details">
                        <div class="card-header alpha-grey header-elements-inline">
                            <h6 class="card-title"><i class="fa fa-clipboard mr-1"></i> <b>Booking Overview</b></h6>
                            <div class="header-elements">
                                <div class="list-icons">
                                    <a class="list-icons-item rotate-180" data-action="collapse"></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    @if($booking->guest)
                                        <p>
                                            <a href="/guests/{{$booking->guest->details->id}}" title=""
                                               class="text-danger"><b>{{$booking->guest ? $booking->guest->details->full_name : '--'}}</b></a>
                                            <br/>
                                            @if ($booking->guest->details->client_number)
                                                Client ID: <b
                                                    class="text-info">{{ $booking->guest->details->client_number }}</b>
                                                <br/>
                                            @endif
                                            @if($booking->guest->details->street != '')
                                                {{$booking->guest->details->street}}
                                                , {{$booking->guest->details->city}} {{$booking->guest->details->zip}}
                                                <br/>
                                            @endif
                                            @if($booking->guest->details->phone != '' && $booking->guest->details->phone != '---')
                                                Phone: +{{$booking->guest->details->phone}}
                                                <br/>
                                            @endif
                                            {!! $booking->guest->details->country != '' ? $booking->guest->details->country .'<br />' : '' !!}
                                            <a href="mailto:{{$booking->guest->details->email}}" title="" class="text-danger">{{$booking->guest->details->email}}</a>
                                        </p>
                                    @endif
                                </div>
                                <div class="text-muted text-uppercase font-size-sm d-flex flex-column justify-content-end align-items-start">
                                    <span class="ml-auto">Created on <b class="text-slate">{{$booking->created_at->format('d.m.Y H:i:s')}}</b></span>
                                    <span class="ml-auto">
                                        Channel: <b class="text-slate">{{$booking->channel}}</b> |
                                        Source: <b class="text-slate">{!! $booking->show_source() !!}</b>
                                    </span>
                                    <span class="ml-auto">Booking ID: <b class="text-slate">{{$booking->id}}</b></span>
                                    <span class="ml-auto">Booking Ref: <b class="text-slate">#{{$booking->ref}}</b></span>
                                    @can('view affiliation')
                                        @if ($booking->affiliation)
                                            <span class="ml-auto">Affiliation: <b class="text-slate">{{$booking->affiliation ? $booking->affiliation->name : '-'}}</b></span>
                                        @endif
                                    @endif
                                    @if($booking->payment->status == 'DUE')
                                        <span class="ml-auto mb-1">
                                            DEPOSIT EXPIRY: <b class="text-slate">{{$booking->deposit_expiry ? $booking->deposit_expiry->format('d.m.Y') : '---'}}</b>
                                        </span>
                                    @endif
                                    @if(!$is_deleted)
                                        <span
                                            class="ml-auto badge {{$booking->status_badge}} text-uppercase font-size-sm">
                                            {{$booking->booking_status}}
                                        </span>
                                    @else
                                        <span class="ml-auto badge bg-danger text-uppercase font-size-sm">DELETED</span>
                                    @endif
                                </div>
                            </div>

                            @if ($booking->status == 'CANCELLED')
                                <div class="cancel-box mt-2">
                                    <p class="mb-0">Cancellation reason: <b>{{ $booking->cancellation->reason ?? $booking->cancel_reason }}</b></p>
                                </div>
                            @endif
                        </div>

                        <div class="card-body p-0">
                            @if($booking->voucher && $booking->voucher != '')
                                <div class="d-block text-center p-2 bg-kima">
                                    <i class="fa fa-fw fa-credit-card"></i> VOUCHER CODE : <b>{{$booking->voucher}}</b>
                                </div>
                            @endif
                            <form action="/bookings/{{$booking->ref}}" method="post" encytype="multipart/form-data" class="table-responsive" id="booking-details">
                                <table class="table table-xs new">
                                    <thead>
                                    <tr class="bg-grey-700">
                                        <th style="min-width: 160px">Guest</th>
                                        <th>Product/Package</th>
                                        <th>Bathroom</th>
                                        <th style="min-width: 160px;">Stay Date</th>
                                        <th style="min-width: 140px;">Duration</th>
                                        <th class="text-center">Amount/Qty</th>
                                        <th class="text-right">Price</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($booking->rooms_count)
                                        @foreach($booking->rooms as $room)
                                            <tr>
                                                <td>
                                                    @if (! blank($room->guestDetails?->details->check_in_at))
                                                        <i class="fas fa-fw fa-circle-check mr-1 text-success tippy" data-tippy-content="{{ $room->guestDetails?->details->check_in_at->format('d.m.Y H:i') }}"></i>
                                                    @endif
                                                    <b>{{ $room->guestDetails?->details->details->full_name ?? '-' }}</b>
                                                </td>
                                                <td>
                                                    <i class="fa fa-bed fa-fw mr-1 tippy" data-tippy-content="Room"></i>
                                                    <b>{{$role == 4 ? $room->subroom->agent_name : $room->room->name .': '. $room->subroom->name}}</b> {!! $room->is_private ? '<i class="fa fa-fw fa-lock tippy" data-tippy-content="Private Booking"></i>' : '' !!}
                                                    {{$room->bed_type}}
                                                </td>
                                                <td>{{$room->bathroom}}</td>
                                                <td>{{date('d.m.y', strtotime($room->from))}} <i class="icon-arrow-right5 font-size-sm"></i> {{date('d.m.y', strtotime($room->to))}}
                                                </td>
                                                <td colspan="2">{{$room->days}} days / {{$room->nights}} nights</td>
                                                <td class="text-right">
                                                    <div class="d-flex justify-content-end">
                                                        <div class="input-group input-group-sm" style="width: 110px">
                                                                <span class="input-group-prepend">
                                                                    <span class="input-group-text">&euro;</span>
                                                                </span>
                                                            <input type="text" name="room_price[{{$room->id}}]"
                                                                   class="form-control form-control-sm"
                                                                   id="current-room-price"
                                                                   value="{{$booking->parsePrice($room->price)}}" {{!auth()->user()->can('edit prices') || $is_deleted ? 'readonly' : ''}} />
                                                            <input type="hidden" name="old_room_price[{{$room->id}}]"
                                                                   value="{{$room->price}}"/>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @if($room->discounts())
                                                @foreach($room->discounts as $offer)
                                                    <tr>
                                                        <td colspan="6">
                                                            <i class="fa fa-fw fa-dollar-sign mr-1 text-danger-300 tippy"
                                                               data-tippy-content="Special Offer"></i> Special Offer:
                                                            {{$offer->offer->name}}
                                                            ({!! $offer->offer->discount_type == 'Percent' ? $offer->offer->discount_value .'%' : '&euro;'. $booking->parsePrice($offer->offer->discount_value) !!}
                                                            )
                                                        </td>
                                                        <td>
                                                            @if(can_see_price())
                                                                <div
                                                                    class="d-flex justify-content-end align-items-center">
                                                                    <div class="input-group input-group-sm"
                                                                         style="width: 110px">
                                                                            <span class="input-group-prepend">
                                                                                <span
                                                                                    class="input-group-text">&euro;</span>
                                                                            </span>
                                                                        <input type="text" name="offer[{{$offer->id}}]"
                                                                               class="form-control form-control-sm"
                                                                               value="{{floatVal($booking->parsePrice($offer->discount_value))}}" {{!auth()->user()->can('edit prices') || $is_deleted ? 'readonly' : ''}} />
                                                                    </div>
                                                                </div>
                                                            @else
                                                                --
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif

                                            @if($room->addons->count() > 0)
                                                @foreach($room->addons as $index => $addon)
                                                    <tr>
                                                        <td colspan="4">
                                                            <i class="fa fa-gift fa-fw mr-1 text-danger-300 tippy"
                                                               data-tippy-content="Extra / Addon"></i> {{$addon->details->name}}{{ $addon->info != '' ? '. '. $addon->info : '' }}
                                                        </td>
                                                        <td class="text-left">
                                                            @if($addon->details->rate_type == 'Day')
                                                                {{intVal($addon->amount)}} {{$addon->details->unit_name}}
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            {{$addon->guests}} <i class="far fa-user"></i>
                                                        </td>
                                                        <td class="text-right">
                                                            @can('see prices')
                                                                <div
                                                                    class="d-flex justify-content-end align-items-center">
                                                                    <div class="input-group input-group-sm"
                                                                         style="width: 110px">
                                                                            <span class="input-group-prepend">
                                                                                <span
                                                                                    class="input-group-text">&euro;</span>
                                                                            </span>
                                                                        <input type="text"
                                                                               name="addon[{{$addon->id}}][price]"
                                                                               class="form-control form-control-sm"
                                                                               value="{{$booking->parsePrice($addon->price)}}" {{!auth()->user()->can('edit prices') || $is_deleted ? 'readonly' : ''}} />
                                                                    </div>
                                                                </div>
                                                            @else
                                                                --
                                                            @endcan
                                                        </td>
                                                    </tr>

                                                    @if($addon->details->questionnaire)
                                                        <tr>
                                                            <td colspan="7">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <i class="fa fa-arrow-turn-down-right fa-fw mr-1 text-danger-300 tippy"
                                                                           data-tippy-content="Questionnaire"></i>
                                                                        {{ $addon->details->questionnaire->name }}
                                                                        @if ($addon->questionnaire_answers && is_array($addon->questionnaire_answers))
                                                                            - <b>{{ implode(', ', $addon->questionnaire_answers) }}</b>
                                                                        @else
                                                                            - No answer
                                                                        @endif
                                                                        &nbsp;
                                                                        <a href="javascript:" class="text-danger-300 font-weight-bold update-answer"
                                                                              data-addon_id="{{$addon->id}}"
                                                                              data-user_answers="{{ $addon->questionnaire_answers ? json_encode($addon->questionnaire_answers) : '' }}"
                                                                              data-questionnaire_answers="{{ json_encode($addon->details->questionnaire->answers->pluck('id', 'answer')->toArray())}}"
                                                                              data-type="{{$addon->details->questionnaire->type->name}}"
                                                                        >Edit</a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7">No guest / room added yet.</td>
                                        </tr>
                                    @endif
                                    @if($booking->transfers_count > 0)
                                        @foreach($booking->transfers as $transfer)
                                            <tr>
                                                <td colspan="5">
                                                    <a href="javascript:" title="" class="text-grey-800 text-link"
                                                       data-toggle="modal"
                                                       data-target="#edit-transfer-{{ $transfer->id }}">
                                                        <i class="fa fa-fw fa-plane-{{$transfer->details->direction == 'Inbound' ? 'arrival' : 'departure'}} mr-1 text-danger-300"></i> {{$transfer->details->name}}
                                                    </a>
                                                    {!! $transfer->flight_detail !!}
                                                </td>
                                                <td class="text-center">{{$transfer->guests}} <i
                                                        class="far fa-user"></i></td>
                                                <td class="text-right">
                                                    @can('see prices')
                                                        <div class="d-flex justify-content-end align-items-center">
                                                            <div class="input-group input-group-sm"
                                                                 style="width: 110px">
                                                                    <span class="input-group-prepend">
                                                                        <span class="input-group-text">&euro;</span>
                                                                    </span>
                                                                <input type="text"
                                                                       name="transfers[{{$transfer->id}}][price]"
                                                                       class="form-control form-control-sm"
                                                                       value="{{$booking->parsePrice($transfer->price)}}" {{!auth()->user()->can('edit prices') || $is_deleted ? 'readonly' : ''}} />
                                                            </div>
                                                        </div>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr>
                                        <td class="text-right" colspan="6"><b>SUBTOTAL</b></td>
                                        <td class="text-right">
                                            @can('see prices')
                                                <b>&euro;{{number_format($booking->parsePrice($booking->total_price), 2)}}</b>
                                            @else
                                                --
                                            @endcan
                                        </td>
                                    </tr>
                                    @if($booking->discounts)
                                        @foreach($booking->discounts as $disc)
                                            <tr>
                                                <td class="text-left" colspan="6">
                                                    <a href="javascript:" title="" class="text-grey-800 text-link"
                                                       data-toggle="{{!$booking->archived ? 'modal' : ''}}"
                                                       data-target="#edit-discount-{{$disc->id}}">
                                                        <i class="fa fa-fw fa-tags mr-1 text-danger-300"></i>Discount: {{$disc->name}}
                                                        ({{$disc->type == 'Percent' ? $disc->value .'%' : 'fixed'}}{{$disc->type == 'Percent' ? ($disc->apply_to == 'ALL' ? ' - Full' : ' - Room only') : ''}}
                                                        )
                                                    </a>
                                                </td>
                                                <td class="text-right">
                                                    @can('see prices')
                                                        @if(strtoupper($disc->name) != 'CANCELLATION FEE' && strtoupper($disc->name) != 'REFUND')
                                                            @if($disc->type == 'Percent')
                                                                @if($disc->apply_to == 'ALL')
                                                                    <b>-
                                                                        &euro;{{number_format($booking->total_price * ($disc->value / 100), 2)}}</b>
                                                                @else
                                                                    <b>-
                                                                        &euro;{{number_format($booking->subtotal * ($disc->value / 100), 2)}}</b>
                                                                @endif
                                                            @else
                                                                <b>-
                                                                    &euro;{{number_format($booking->parsePrice($disc->value))}}</b>
                                                            @endif
                                                        @else
                                                            @if($disc->value < 0)
                                                                <b>&euro;{{number_format($disc->value) - ($disc->value * 2)}}</b>
                                                            @else
                                                                <b>&euro;{{number_format($booking->parsePrice($disc->value))}}</b>
                                                            @endif
                                                        @endif
                                                    @else
                                                        --
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    @if ($tax['exclusives']['total'] && count($tax['exclusives']['taxes']))
                                        @foreach ($tax['exclusives']['taxes'] as $tax)
                                            @php
                                                $ext_amount = \App\Services\Booking\TaxService::calculateExclusiveTax($booking->subtotal_with_discount, $tax->rate, $tax->type);
                                                $ext_tax += $ext_amount;
                                            @endphp
                                            <tr>
                                                <td class="text-right" colspan="6"><b>{{ strtoupper($tax->name) }} ({{ number_format($tax->rate, 0) }}%)</b></td>
                                                <td class="text-right">
                                                    @can('see prices')
                                                        <b>&euro;{{ parsePrice($ext_amount) }}</b>
                                                    @else
                                                        --
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr>
                                        <td class="text-right" colspan="6"><b>PROCESSING FEE</b></td>
                                        <td class="text-right">
                                            @can('see prices')
                                                <div class="d-flex justify-content-end">
                                                    <div class="input-group input-group-sm" style="width: 110px">
                                                        <span class="input-group-prepend">
                                                            <span class="input-group-text">&euro;</span>
                                                        </span>
                                                        <input type="text" name="processing_fee"
                                                            class="form-control form-control-sm"
                                                            id="processing_fee"
                                                            value="{{ $booking->parsePrice($booking->payment->processing_fee) }}" {{ !auth()->user()->can('edit prices') || $is_deleted ? 'readonly' : '' }} />
                                                    </div>
                                                </div>
                                            @else
                                                --
                                            @endcan
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="6"><b>GRAND TOTAL</b></td>
                                        <td class="text-right">
                                            @can('see prices')
                                                <b>&euro;{{number_format($booking->parsePrice($booking->grand_total + $ext_tax), 2)}}</b>
                                            @else
                                                --
                                            @endcan
                                        </td>
                                    </tr>
                                    @if(in_array($booking->payment->methods, ['stripe']))
                                        <tr>
                                            <td class="text-right" colspan="6"><b>PAYMENT PROCESSING FEE</b></td>
                                            <td class="text-right">
                                                @can('see prices')
                                                    <b>&euro;{{number_format($booking->payment->processing_fee, 2)}}</b>
                                                @else
                                                    --
                                                @endcan
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!$booking_taxes['empty'])
                                        @php $show_vat = false; @endphp
                                        @foreach ($booking_taxes['vat'] as $type => $arr)
                                            @foreach ($arr as $vat_tax)
                                                @if ($vat_tax['amount'])
                                                    <tr>
                                                        <td class="text-right" colspan="6" valign="top" style="vertical-align: top;">
                                                            {!! !$show_vat ? '<b>VAT</b>' : '' !!}
                                                        </td>
                                                        <td class="text-right">
                                                            @can('see prices')
                                                                {{ $vat_tax['rate'] }}
                                                                <b>&euro;{{ parsePrice($vat_tax['amount']) }}</b>
                                                            @else
                                                                --
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                    @php $show_vat = true; @endphp
                                                @endif
                                            @endforeach
                                        @endforeach
                                    @endif
                                    @if($booking->status != 'DRAFT' && $booking->payment->status == 'DUE' && $booking->payment->status == 'EXPIRED')
                                        <tr>
                                            <td class="text-right" colspan="6"><b>DEPOSIT
                                                    (DUE {{$booking->deposit_expiry->format('d.m.Y')}})</b></td>
                                            <td class="text-right">
                                                <b>&euro;{{number_format($booking->deposit_amount, 2)}}</b></td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="text-right" colspan="6"><b>TOTAL PAID</b></td>
                                        <td class="text-right">
                                            @can('see prices')
                                                <b>&euro;{{number_format($booking->payment->total_paid, 2)}}</b>
                                            @else
                                                --
                                            @endcan
                                        </td>
                                    </tr>
                                    @if(auth()->user()->hasRole('Agent') || $booking->agent_id)
                                        <tr>
                                            <td class="text-right" colspan="6"><b>AGENT COMMISSION</b></td>
                                            <td class="text-right">
                                                @can('see prices')
                                                    {{$booking->agent->commission_value}}%
                                                    <b>&euro;{{number_format($booking->commission, 2)}}</b>
                                                @else
                                                    --
                                                @endcan
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="text-right text-danger" colspan="6"><b>OPEN BALANCE</b></td>
                                        <td class="text-right text-danger">
                                            <b>&euro;{{ number_format($booking->payment->open_balance, 2) }}</b></td>
                                    </tr>
                                    </tbody>
                                </table>

                                @if($booking->rooms_count > 0 && !$is_deleted)
                                    @can('edit prices')
                                        <div class="d-flex justify-content-end p-3">
                                            @csrf
                                            <button class="btn bg-danger-400 btn-booking-price"><i
                                                    class="far fa-fw fa-check"></i> Update Price
                                            </button>
                                        </div>
                                    @endcan
                                @endif
                            </form>
                        </div>
                    </div>

                    @if($booking->status == 'CONFIRMED' || auth()->user()->can('add payment'))
                        <div class="card">
                            <div class="card-header alpha-grey header-elements-inline">
                                <h6 class="card-title"><i class="fa fa-dollar-sign mr-1"></i> <b>Payment</b></h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item rotate-180" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body border-0 p-0">
                                <div class="text-uppercase font-size-lg p-3"><b>Payment History</b></div>
                                <form action="/bookings/add-payment" method="post" id="new-payment" class="table-responsive">
                                    <table class="table table-xs">
                                        <thead>
                                        <tr class="alpha-grey border-top-1 border-alpha-grey border-bottom-1">
                                            <th class="text-uppercase py-2 px-3">Date</th>
                                            <th class="text-uppercase py-2 px-3">Method</th>
                                            <th class="text-uppercase py-2 px-3 text-right">Amount</th>
                                            <th class="text-uppercase py-2 px-3">Paid at</th>
                                            <th class="text-uppercase py-2 px-3">Verified</th>
                                            <th class="text-uppercase py-2 px-3">Invoice</th>
                                            <th class="text-right"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if($booking->payment->records->count() <= 0)
                                            <tr>
                                                <td colspan="7">No payment records yet.</td>
                                            </tr>
                                        @else
                                            @foreach($booking->payment->records as $record)
                                                <tr>
                                                    <td style="min-width: 130px;">{{$record->created_at->format('d.m.y H:i')}}</td>
                                                    <td><span class="text-uppercase"><b>{{$record->methods}}</b></span></td>
                                                    <td class="text-center">
                                                        @can('see prices')
                                                            <div class="d-flex justify-content-end">
                                                                <div class="input-group input-group-sm" style="width: 126px">
                                                                    <span class="input-group-prepend"><span class="input-group-text"><i class="fa fa-euro-sign"></i></span></span>
                                                                    <input type="text" name="price" class="form-control form-control-sm payment-record-{{$record->id}}" placeholder="0.0" value="{{$booking->parsePrice($record->amount)}}"/>
                                                                    <span class="input-group-append">
                                                                        <button {{$booking->archived ? 'disabled' : ''}} class="btn btn-sm btn-danger admin-update-payment-record {{$booking->archived ? 'disabled' : ''}}" data-id="{{$record->id}}"><i class="far fa-check"></i></button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <b>--</b>
                                                        @endcan
                                                    </td>
                                                    <td>{{!is_null($record->paid_at) ? $record->paid_at->format('d.m.Y') : '-'}}</td>
                                                    <td>
                                                        @if (
                                                                ($record->methods == 'stripe' && $record->stripe && $record->stripe->status == 'paid') ||
                                                                ($record->methods == 'paypal' && $record->paypal && $record->paypal->status == 'COMPLETED')
                                                            )
                                                            SYSTEM
                                                        @else
                                                            <b>{{$record->verify_by ? $record->user->name : '--'}}</b>
                                                        @endif
                                                        {{$record->verified_at ? 'at '. $record->verified_at->format('d.m.y H:i') : '--'}}
                                                        @if ($record->methods == 'stripe' && $record->stripe && $record->stripe->status == 'paid')
                                                            <br/>
                                                            <code class="p-0">{{$record->stripe->intent}}</code>
                                                        @endif
                                                        @if ($record->methods == 'paypal' && $record->paypal && $record->paypal->status == 'COMPLETED')
                                                            <br/>
                                                            <code class="p-0">{{$record->paypal->order_id}}</code>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('tenant.bookings.downloadInvoicePayment', ['ref' => $booking->ref, 'payment_transfer' => $record, 'id' => $loop->iteration]) . (request()->has('preview') ? '?preview' : '') }}" class="btn btn-sm btn-danger tippy" data-tippy-content="Download Invoice">
                                                            <i class="fa fa-fw fa-download"></i>
                                                        </a>
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="d-flex justify-content-end align-items-center">
                                                            @if(!$record->verify_by && !$record->verified_at)
                                                                <button title="" class="btn btn-sm bg-grey-600 py-1" v-on:click.prevent="loadPaymentRecord({{$record->id}})">VIEW</button>
                                                            @else
                                                                <a href="#" class="btn btn-sm alpha-grey btn-disabled text-muted tippy" data-tippy-content="Verified"><i class="fal fa-check text-success"></i></span>
                                                                </a>
                                                                @can('send payment confirmation')
                                                                    <button
                                                                        class="btn btn-sm btn-danger tippy confirm-dialog send-confirmed-payment-email"
                                                                        data-tippy-content="resend confirmed payment email"
                                                                        data-text="Send confirmed payment email to this guest?"
                                                                        data-payment-id="{{$booking->payment->id}}"
                                                                        data-transfer-id="{{$record->id}}"
                                                                        data-index="{{$loop->iteration}}"
                                                                    >
                                                                        <i class="fa fa-fw fa-envelope"></i>
                                                                    </button>
                                                                @endcan
                                                            @endif
                                                            @can('delete payment')
                                                                <a href="/payments/{{$record->id}}/delete/{{$booking->ref}}" class="text-danger ml-2 tippy confirm-dialog" data-tippy-content="Delete record" data-text="DELETE RECORD? This action will be logged"><i class="fa fa-times fa-fw"></i></a>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @if(!$is_deleted)
                                            @can('add payment')
                                                <tr class="alpha-grey">
                                                    <td colspan="7" class="px-3 py-2"><i class="fal fa-plus fa-fw"></i>
                                                        <b>NEW PAYMENT</b>
                                                    </td>
                                                </tr>
                                                <tr class="alpha-grey">
                                                    <th colspan="3"><b>METHOD</b></th>
                                                    <th><b>AMOUNT</b></th>
                                                    <th colspan="2"><b>PAID AT</b></th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="py-3">
                                                        <select class="form-control select-no-search form-control-sm" data-container-css-class="select-sm" data-fouc data-placeholder="Method" name="methods">
                                                            <option value="cash">CASH</option>
                                                            <option value="creditcard">CREDIT CARD</option>
                                                            <option value="banktransfer">BANK TRANSFER</option>
                                                            <option value="ec-zahlung">EC-ZAHLUNG</option>
                                                            <option value="refund">REFUND</option>
                                                        </select>
                                                    </td>
                                                    <td class="py-3">
                                                        <input type="text" name="amount" class="form-control form-control-sm amount-payment" placeholder="amount" style="width: 100px;" required/>
                                                    </td>
                                                    <td class="py-3" colspan="2">
                                                        <div class="input-group input-group-sm" style="width: 150px">
                                                            <span class="input-group-prepend">
                                                                <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                                            </span>
                                                            <input type="text" class="form-control date-basic" data-ref="{{$booking->ref}}" value="{{date('d.m.Y')}}" name="paid_at">
                                                        </div>
                                                    </td>
                                                    <td class="text-right py-3">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{$booking->payment->id}}"/>
                                                        <button type="submit" class="btn btn-sm bg-grey-600 py-1 btn-add-payment">ADD</button>
                                                    </td>
                                                </tr>
                                            @endcan
                                        @endif
                                        </tbody>
                                    </table>
                                </form>
                            </div>

                        </div>
                    @endif

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header alpha-grey header-elements-inline">
                                    <h6 class="card-title"><i class="far fa-history mr-1"></i> <b>Booking History</b>
                                    </h6>
                                    <div class="header-elements">
                                        <div class="list-icons">
                                            <a class="list-icons-item rotate-180" data-action="collapse"></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                    <div class="list-feed">
                                        @foreach($booking->histories as $history)
                                            @if($booking->archived && $history->containsAmount)
                                            @else
                                                <div class="list-feed-item border-{{$history->info_type}}">
                                                    <div
                                                        class="text-muted">{{$history->created_at->format('M d, H:i:s')}}</div>
                                                    {!! $history->details !!}
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header alpha-grey header-elements-inline">
                                    <h6 class="card-title"><i class="far fa-comment-dots mr-1"></i> <b>Internal
                                            Notes</b></h6>
                                    <div class="header-elements">
                                        <div class="list-icons">
                                            <a class="list-icons-item rotate-180" data-action="collapse"></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body internal-notes">
                                    <div class="list-feed" v-if="notes && notes.data && notes.data.length > 0">
                                        <div class="list-feed-item" v-for="note in notes.data" v-key="note.id">
                                            <div class="text-muted">@{{note.date}}</div>
                                            <i class="far fa-fw fa-comment-dots"></i> <b>@{{note.user}}</b> : <i>@{{note.message}}</i>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <em>No notes yet.</em>
                                    </div>
                                </div>
                                @if(!$is_deleted)
                                    <div class="card-body">
                                        <label class="mb-2"><b>Add new note</b></label>
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <textarea class="form-control form-control-sm" rows="3"
                                                          placeholder="write some note"
                                                          v-model="notes.message"></textarea>
                                            </div>
                                        </div>
                                        @can('add notes')
                                            <div class="form-group row mb-0">
                                                <div class="col-12">
                                                    <div class="justify-content-end d-flex">
                                                        <button class="bg-grey-600 btn"
                                                                @click="postNote({{$booking->id}})">Submit
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <div id="questionnaire-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-grey-800">
                    <h5 class="modal-title">Edit Answer</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('tenant.bookings.updateQuestionnaireAnswer') }}" class="form-horizontal" method="post">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">Answers</label>
                            <div class="col-sm-9" id="answers">

                            </div>
                        </div>
                        <input type="hidden" name="addon_id" id="addon_id_input"  value=""/>
                    </div>

                    @can ('edit booking')
                        <div class="modal-footer">
                            @csrf
                            <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-danger">Update Answer</button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>


    <script>
        window.bookingID = {{$booking->id}};
    </script>
@endsection

@section('scripts')
    <script>
        window.bookingRef = '{{$booking->ref}}';
        window.guestEmail = '{{$booking->guest?->details?->email}}';
        $('.date-time-picker').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            autoApply: true,
            locale: {
                cancelLabel: 'Clear',
                format: 'DD.MM.YYYY HH:mm'
            }
        });

        $('.date-time-picker').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD.MM.YYYY HH:mm'));
        });

        window.choices = [];

        $('.js-choice').each((index, elem) => {
            let maxItemCount = $(elem).prop("tagName") == 'INPUT' ? 1 : -1;

            window.choices.push(new Choices(elem, {
                removeItemButton: true,
                maxItemCount
            }));
        })

    </script>
@endsection
