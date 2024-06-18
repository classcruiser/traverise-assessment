@php
    use App\Services\Booking\TaxService;

    $total_exc_tax = 0;
@endphp

@extends('Booking.main')
@section('customcss')
<style type="text/css" media="screen">
    body {
        background-color: {{ $tenant_settings->bg_color }};
    }
    .bg-custom-primary {
        background-color: {{ $tenant_settings->primary_color }};
    }
    .bg-custom-secondary {
        background-color: {{ $tenant_settings->secondary_color }};
    }
    .navi li span { color: #444 }
    .navi li.navi-active span {
        border-color: {{ $tenant_settings->accent_color }};
        color: {{ $tenant_settings->accent_color }};
    }
    .navi li.navi-complete:after {
        background-color: {{ $tenant_settings->accent_color }};
    }
    .btn-custom {
        background-color: {{ $tenant_settings->accent_color }};
    }
    .text-custom, .link-custom, .normal-text a {
        color: {{ $tenant_settings->accent_color }};
    }
    .text-custom:hover, .link-custom:hover, .normal-text a:hover {
        color: {{ $tenant_settings->accent_color }};
    }
    .border-danger {
        border-color: {{ $tenant_settings->accent_color }};
    }
    .bg-transparent { background: transparent; box-shadow: none; }
</style>
@endsection

@section('content')
    @include('Booking.partials.booknow.popup')
    <div class="header fixed md:absolute h-[400px] left-0 top-0 w-full" style="background: url({{url('bucket/'. tenant('id') .'.jpg')}}) {{$tenant_settings->bg_pos_horizontal}} {{$tenant_settings->bg_pos_vertical}} no-repeat; background-size: cover;">
        &nbsp;
    </div>
    <div class="container relative z-30 sm:mt-0 md:mt-32">
        <div class="pt-4 px-4 block md:flex justify-between items-end w-full">
            <div class="px-3">
                <h1 class="text-white text-3xl font-bold">
                    @if ($step > 1)
                        @if ($location->title != '')
                            {{ $location->title }}
                        @else
                            {{ $tenant_settings->title }}
                        @endif
                    @else
                        {{ $tenant_settings->title }}
                    @endif
                </h1>
                <div class="text-white opacity-80">
                    @if ($step > 1)
                        @if ($location->subtitle != '')
                            {!! $location->subtitle !!}
                        @else
                            {!! $tenant_settings->short_description !!}
                        @endif
                    @else
                        {!! $tenant_settings->subtitle !!}
                    @endif
                </div>
            </div>
            <div class="px-3 text-right text-white opacity-80 hidden md:block">
                <p class="leading-relaxed">
                    <i class="fa fa-user mr-1"></i> {{$tenant_settings->contact_person}}<br />
                    {!! nl2br($tenant_settings->address) !!}<br />
                    <i class="fa fa-envelope mr-1"></i> {!! $tenant_settings->contact_email !!}<br />
                    <i class="fa fa-phone mr-1"></i> {!! $tenant_settings->phone_number !!}
                </p>
            </div>
        </div>
    </div>
    <div class="page-content z-30">
        <div class="content-wrapper container">
            <div class="content">
                <div class="card border-0 rounded rounded-bottom-0 bg-transparent">

                    @if($step == 2)
                        <div class="card-body bg-custom-primary py-2 px-2">
                            <form id="update_dates" action="/book-now/update-dates" method="post">
                                <div class="flex flex-col md:flex-row justify-center items-center space-y-2 md:space-y-0 space-x-0 md:space-x-2">
                                    <div class="form-group mb-0 w-full md:w-[260px] mx-1">
                                        <div class="input-group input-cal-step2 w-full">
                                            <span class="input-group-prepend mobile-hide">
                                                <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                            </span>
                                            <input id="datepicker" name="dates" type="text" value="{{$default_check_in .' - '. $default_check_out}}" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group mb-0 ml-0 md:ml-2 w-full md:w-[170px] mx-1">
                                        <div class="input-group input-guest-step2 w-full">
                                            <span class="input-group-prepend mobile-hide">
                                                <span class="input-group-text"><i class="icon-users"></i></span>
                                            </span>
                                            <select name="guest" class="custom-select mobile-radius">
                                                @for($i = 1; $i <= 10; $i++)
                                                    <option value="{{$i}}" {{$i == $guest ? 'selected' : ''}}>{{$i}} {{Str::plural('person', $i)}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group ml-0 md:ml-2 mb-0 w-full md:w-[160px] mx-1">
                                        @csrf
                                        <button class="btn btn-custom check_duration w-full" data-minimum="{{$location->minimum_nights}}">UPDATE</button>
                                    </div>
                                    <!-- end -->
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="card-body bg-transparent py-2 mobile-navi">
                        <div class="navi">
                            <ul>
                                <li class="{{($step >= 1) ? 'navi-active' : ''}} {{$step >= 2 ? 'navi-complete' : ''}}">
                                    {!! ($step >= 2) ? '<span><i class="fa fa-check"></i></span>' : '<span>1</span>' !!}
                                </li>
                                <li class="{{($step >= 2) ? 'navi-active' : ''}} {{$step >= 3 ? 'navi-complete' : ''}}">
                                    {!! ($step >= 3) ? '<span><i class="fa fa-check"></i></span>' : '<span>2</span>' !!}
                                </li>
                                <li class="{{($step >= 3) ? 'navi-active' : ''}} {{$step >= 4 ? 'navi-complete' : ''}}">
                                    {!! ($step >= 4) ? '<span><i class="fa fa-check"></i></span>' : '<span>3</span>' !!}
                                </li>
                                <li class="{{($step >= 4) ? 'navi-active' : ''}} {{$step >= 5 ? 'navi-complete' : ''}}">
                                    {!! ($step >= 5) ? '<span><i class="fa fa-check"></i></span>' : '<span>4</span>' !!}
                                </li>
                                <li class="{{($step >= 5) ? 'navi-active' : ''}} {{$step >= 6 ? 'navi-complete' : ''}}">
                                    {!! ($step >= 6) ? '<span><i class="fa fa-check"></i></span>' : '<span>5</span>' !!}
                                </li>
                                <li class="{{($step >= 6) ? 'navi-active' : ''}} {{$step >= 6 ? 'navi-complete' : ''}}">
                                    {!! ($step >= 6) ? '<span><i class="fa fa-check"></i></span>' : '<span>6</span>' !!}
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-8">

                        @include('Booking.partials.booknow.step1')
                        @include('Booking.partials.booknow.step2')
                        @include('Booking.partials.booknow.step3')
                        @include('Booking.partials.booknow.step4')
                        @include('Booking.partials.booknow.step5')
                        @include('Booking.partials.booknow.step6')

                    </div>

                    <div class="col-md-4">

                        @if($step >= 2 && $step < 6)
                            <div class="card sidebar-stick" data-margin-top="94">
                                <div class="card-header bg-transparent text-center sidebar-title">
                                    <b>{{$location->name}}</b>
                                </div>
                                <div class="card-body">
                                    <div class="text-muted d-flex justify-content-between align-items-center text-uppercase font-size-sm">
                                        <span>Check In</span>
                                        <span>Check Out</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-uppercase font-size-xl">
                                        <span><b>{{$default_check_in}}</b></span>
                                        <i class="fal fa-arrow-right fa-fw text-muted"></i>
                                        <span><b>{{$default_check_out}}</b></span>
                                    </div>
                                    @if($step < 6)
                                        <div class="text-center mt-2">
                                            <a href="/book-now/rooms" title="" class="btn btn-custom btn-sm confirm-dialog text-uppercase py-0" data-text="Changing check-in / check-out dates requires clearing your existing room bookings. Would you like to continue?">
                                                change
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                @if($step >= 3)
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-end">
                                            <div class="w-100 mr-1">
                                                <div class="font-size-xl text-custom"><b>{{$booking_room['name']}}</b></div>
                                                <span>{{$booking_room['guest'] .' '. Str::plural('guest', $booking_room['guest'])}}</span>, <span id="bed-type">{{$booking_room['bed_type']}}</span> <span>bed type</span><br />
                                                <div class="w-100 d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span>Accommodation</span>
                                                        <span id="surcharge-info">
                                                            @if(session('room')['private_booking'])
                                                                <br />&euro;{{$booking_room['empty_fee']}} <b><em>Private Room Surcharge</em></b>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div style="font-size: 1.2em;"><b id="roomprice-total">&euro;{{number_format($booking_room['accommodation_price'], 2)}}</b></div>
                                                </div>
                                                @if(!is_null($booking_room['special_offer']))
                                                    <div class="w-100 d-flex justify-content-between align-items-center">
                                                        <div class="text-uppercase font-weight-bold">
                                                            <span>
                                                                {{$booking_room['special_offer']['offer'] .' '. $booking_room['special_offer']['type']}}
                                                            </span>
                                                        </div>
                                                        <div style="font-size: 1.2em;">- <b>&euro;{{number_format($booking_room['offer_discount'], 2)}}</b></div>
                                                    </div>
                                                @endif
                                                @if($tax['exclusives']['total'] > 0)
                                                    @foreach ($tax['exclusives']['taxes'] as $exc_tax)
                                                        @php
                                                            $exc_amount = TaxService::calculateExclusiveTax($booking_room['accommodation_price'], $exc_tax->rate, $exc_tax->type);
                                                            $total_exc_tax += $exc_amount;
                                                        @endphp
                                                        <div class="w-100 d-flex justify-content-between align-items-center">
                                                            <div>{{ $exc_tax->rate }}% {{ $exc_tax->name }}</div>
                                                            <div style="font-size: 1.2em;">
                                                                <b>&euro;{{ parsePrice($exc_amount) }}</b>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            @if($step < 6)
                                                <a href="/book-now" title="" class="confirm-dialog text-muted confirm-dialog" data-text="Removing room. Would you like to continue?"><i class="fal font-size-xl fa-times fa-fw"></i></a>
                                            @endif
                                        </div>
                                        <div class="pt-2 mt-2 {{count($booking_room['addons']) > 0 ? 'border-top-1' : ''}} border-alpha-grey addon-container">
                                            @if(count($booking_room['addons']) > 0)
                                                @foreach($booking_room['addons'] as $addon)
                                                    <div class="d-flex justify-content-between align-items-end py-1 addon-{{$addon['id']}}">
                                                        <div class="mr-1 w-100">
                                                            <div class="font-size-lg"><b>{{$addon['name']}}</b></div>
                                                            <div class="w-100 d-flex justify-content-between align-items-center">
                                                                <span>
                                                                    {{$addon['guests'] .' '. Str::plural($addon['unit_name'], $addon['guests'])}}{{$addon['amount'] > 1 ? ', '. $addon['amount'] .' '. Str::plural('day', $addon['amount']) : ''}}
                                                                    @if (isset($addon['weeks']) && $addon['weeks'] != '' && $addon['weeks'] != 0)
                                                                        . Starts in {{ (new NumberFormatter('en_US', NumberFormatter::ORDINAL))->format(intVal($addon['weeks'])) }} {{ Str::plural('week', intVal($addon['weeks'])) }}
                                                                    @endif
                                                                </span>
                                                                <span style="font-size: 1.1em;"><b>{!! $addon['total'] > 0 ? '&euro;'. number_format($addon['total'], 2) : 'FREE' !!}</b></span>
                                                            </div>
                                                        </div>
                                                        @if($step < 5)
                                                            <a href="#" title="" class="text-muted remove-addon" data-id="{{$addon['id']}}"><i class="fal font-size-xl fa-times fa-fw"></i></a>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                        <div class="{{count($booking_room['transfers']) > 0 ? 'pt-2 mt-2 border-top-1' : ''}} border-alpha-grey transfer-container">
                                            @if(count($booking_room['transfers']) > 0)
                                                @foreach($booking_room['transfers'] as $transfer)
                                                    <div class="d-flex justify-content-between align-items-end py-1 transfer-{{$transfer['id']}}">
                                                        <div class="mr-1 w-100">
                                                            <div class="font-size-lg"><b>{{$transfer['name']}}</b></div>
                                                            <div class="w-100 d-flex justify-content-between align-items-center">
                                                                <span>
                                                                    {{$transfer['guests'] .' '. Str::plural('guest', $transfer['guests'])}}
                                                                </span>
                                                                <span style="font-size: 1.1em"><b>{!! $transfer['total'] > 0 ? '&euro;'. number_format($transfer['total'], 2) : 'FREE' !!}</b></span>
                                                            </div>
                                                        </div>
                                                        @if($step < 5)
                                                            <a href="#" title="" class="text-muted remove-transfer" data-id="{{$transfer['id']}}"><i class="fal font-size-xl fa-times fa-fw"></i></a>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    @if($step >= 3 && $step < 6)
                                        @if($booking_room['voucher'] != '')
                                            <div class="card-body">
                                                <div><b>VOUCHER CODE</b></div>
                                                <p>Thanks! You have successfully applied the voucher code to your booking.</p>
                                                <p>
                                                    <b>CODE</b>: <b class="text-custom">{{$booking_room['voucher']}}</b>
                                                    @if ($booking_room['voucher_detail']['type'] === 'PERCENTAGE')
                                                        ({{ $booking_room['voucher_detail']['amount'] }}% off for accommodation)
                                                    @else
                                                        (&euro;{{ $booking_room['voucher_detail']['amount'] }} off for accommodation)
                                                    @endif
                                                    @if ($step < 5)
                                                        <br />
                                                        <a href="#" title="" class="cancel-voucher text-grey d-inline-block border-top-0 border-left-0 border-right-0 border-dashed border-bottom-1">cancel ?</a>
                                                    @endif
                                                </p>
                                                <input type="hidden" name="current-url" value="{{request()->url()}}" />
                                            </div>
                                        @elseif($step < 5)
                                            <div class="card-body">
                                                <div><b>VOUCHER CODE</b></div>
                                                <div class="voucher-container">
                                                    <p>Have voucher code? Enter here and it will be applied to your booking.</p>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control text-uppercase" id="voucher" placeholder="Voucher code">
                                                        <span class="input-group-append">
                                                            <button class="btn btn-light" type="button" id="voucher-btn">Apply</button>
                                                        </span>
                                                    </div>
                                                    <div id="voucher-error" class="text-danger mt-2"></div>
                                                </div>
                                                <input type="hidden" name="current-url" value="{{request()->url()}}" />
                                            </div>
                                        @endif
                                        <div class="card-body">
                                            <p>Leave a comment here if you have a specific request:</p>
                                            <textarea name="comment" class="form-control form-control-sm" rows="5">{{ $booking_room['comment'] }}</textarea>
                                            <div class="text-right">
                                                <button class="btn btn-custom text-uppercase font-size-xs mt-2" id="btn-comment">save</button>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="font-size-xl"><b>Total</b></div>
                                            <span class="font-size-xl font-weight-bold text-custom" id="grand-total">
                                                <b>&euro;{{ number_format((floatVal($booking_room['accommodation_price']) + floatVal($total_exc_tax) + floatVal($total_addon) + floatVal($total_transfer)), 2) }}</b>
                                            </span>
                                        </div>
                                        @if($location->hotel_tax && $location->goods_tax)
                                            <span id="tax-info">{!! $tax_info !!}</span>
                                        @endif
                                        @if($tenant_settings->stripe_fee_percentage && $tenant_settings->stripe_fee_fixed)
                                            <div style="font-size: .9em;">
                                                <em>The payment processing fee ({{$tenant_settings->stripe_fee_percentage}}% + &euro;{{$tenant_settings->stripe_fee_fixed}}) will be added in the checkout.</em>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @if($step == 5)
                                    <div class="confirm-sidebar">
                                        <form action="/book-now/confirmed?state={{$state . $ga}}" method="post">
                                            @csrf
                                            <button type="submit" class="btn btn-custom btn-lg w-100">CONFIRM AND PAY</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($step >= 2 && $step < 6)
                            @if($documents && $documents->count())
                                <div class="card sidebar-stick">
                                    <div class="card-body document-list">
                                        @foreach($documents as $document)
                                            <p>
                                                <i class="fal fa-file-alt mr-1"></i> <a href="/doc/{{$document->slug}}" target="{{$document->target}}" title="" {{$document->popup ? 'data-popup' : ''}}>{{$document->name}}</a>
                                            </p>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if($step >= 3 && $step < 6 && session('room.inclusions') != '')
                            <div class="card sidebar-stick" data-margin-top="94">
                                <div class="card-header bg-transparent text-center sidebar-title">
                                    <b>Price Includes</b>
                                </div>
                                <div class="card-body camp-inclusions">
                                    {!! session('room.inclusions') !!}
                                </div>
                            </div>
                        @endif

                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
