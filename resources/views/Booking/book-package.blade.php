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
<div class="header fixed md:absolute h-[400px] left-0 top-0 w-full" style="background: url({{ asset('front/'. tenant('id') .'_header.jpg?'. date('Ymdh')) }}) {{$tenant_settings->bg_pos_horizontal}} {{$tenant_settings->bg_pos_vertical}} no-repeat; background-size: cover;">
    &nbsp;
</div>
<div class="container relative z-30 sm:mt-0 md:mt-32">
    <div class="pt-4 px-4 block md:flex justify-between items-end w-full">
        <div class="px-3">
            <h1 class="text-white text-3xl font-bold">{{ $tenant_settings->title }}</h1>
            <p class="text-white opacity-80">{!! $tenant_settings->short_description !!}</p>
        </div>
        <div class="px-3 text-right text-white opacity-80 hidden md:block">
            <p class="leading-relaxed">
                <i class="fa fa-user mr-1"></i> {{ $tenant_settings->contact_person }}<br />
                {!! nl2br($tenant_settings->address) !!}<br />
                <i class="fa fa-envelope mr-1"></i> {!! $tenant_settings->contact_email !!}<br />
                <i class="fa fa-phone mr-1"></i>  {!! $tenant_settings->phone_number !!}
            </p>
        </div>
    </div>
</div>
<div class="page-content z-30">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card border-0 rounded rounded-bottom-0 bg-transparent">
                @if($step == 1)
                    <div class="card-body bg-grey-800 py-2 px-3">
                        <form action="/book-package/{{$slug}}" method="post">
                            <div class="d-flex justify-content-center align-items-start">
                                <div class="form-group mb-0">
                                    <div class="input-group input-cal-step2">
                                        <span class="input-group-prepend mobile-hide">
                                            <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                        </span>
                                        <input type="text" name="date" class="form-control daterange-single mobile-radius" value="{{$default_check_in}}">
                                    </div>
                                </div>
                                <div class="form-group mb-0 ml-2">
                                    <div class="input-group input-guest-step2">
                                        <span class="input-group-prepend mobile-hide">
                                            <span class="input-group-text"><i class="icon-users"></i></span>
                                        </span>
                                        <select name="guest" class="custom-select mobile-radius">
                                            @for($i = ($package->min_guest ?? 1); $i <= ($package->max_guest ?? 10); $i++)
                                                <option value="{{$i}}" {{$i == $guest ? 'selected' : ''}}>{{$i}} {{\Illuminate\Support\Str::plural('person', $i)}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ml-2 mb-0">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{$package->id}}" />
                                    <button class="btn btn-kima">UPDATE</button>
                                </div>
                                <!-- end -->
                            </div>
                        </form>
                    </div>
                @endif

                <div class="card-body bg-transparent py-2 mobile-navi">
                    <div class="navi">
                        <ul>
                            <li class="{{($step >= 1) ? 'navi-active' : ''}}">
                                {!! ($step >= 2) ? '<span><i class="fa fa-check"></i></span>' : '<span>1</span>' !!}
                            </li>
                            <li class="{{($step >= 2) ? 'navi-active' : ''}}">
                                {!! ($step >= 3) ? '<span><i class="fa fa-check"></i></span>' : '<span>2</span>' !!}
                            </li>
                            <li class="{{($step >= 3) ? 'navi-active' : ''}}">
                                {!! ($step >= 4) ? '<span><i class="fa fa-check"></i></span>' : '<span>3</span>' !!}
                            </li>
                            <li class="{{($step >= 4) ? 'navi-active' : ''}}">
                                {!! ($step >= 5) ? '<span><i class="fa fa-check"></i></span>' : '<span>4</span>' !!}
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-8">
                    @include('Booking.partials.book-package.extras')

                    @include('Booking.partials.book-package.form')

                    @include('Booking.partials.book-package.confirm')

                    @include('Booking.partials.book-package.completed')
                </div>

                <div class="col-md-4">

                    <div class="card sidebar-stick" data-margin-top="20">
                        <div class="card-header bg-transparent text-center sidebar-title">
                            <b>{{$package->name}}</b>
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
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="font-size-xl text-custom"><b>{{$room->name}}</b></div>
                                    <span>{{$guest .' '. \Illuminate\Support\Str::plural('guest', $guest)}}, {{$room->beds[0]}} bed type</span>
                                </div>
                            </div>
                            @if(count($package->addons) > 0)
                                <div class="pt-2 mt-2 border-top-1 border-alpha-grey">
                                    @foreach($package->addons as $addon)
                                        <div class="d-flex justify-content-between align-items-center py-1">
                                            <div class="mr-2">
                                                <div class="font-size-lg"><b>{{$addon->details->name}}</b></div>
                                                <span>
                                                    {{$guest .' '. \Illuminate\Support\Str::plural('guest', $guest)}}{{$addon->qty > 1 ? ', '. $addon->qty .' '. \Illuminate\Support\Str::plural('day', $addon->qty) : ''}}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="pt-2 mt-2 {{count($extras) > 0 ? 'border-top-1' : ''}} border-alpha-grey addon-container">
                                @if(count($extras) > 0)
                                    @foreach($extras as $addon)
                                        <div class="d-flex justify-content-between align-items-end py-1 addon-{{$addon['id']}}">
                                            <div class="mr-1 w-100">
                                                <div class="font-size-lg"><b>{{$addon['name']}}</b></div>
                                                <div class="w-100 d-flex justify-content-between align-items-center">
                                                    <span>
                                                        {{$addon['guests'] .' '. \Illuminate\Support\Str::plural('guest', $addon['guests'])}}{{$addon['amount'] > 1 && $addon['id'] != 15 ? ', '. $addon['amount'] .' '. \Illuminate\Support\Str::plural('day', $addon['amount']) : ''}}
                                                    </span>
                                                    <span class="text-custom" style="font-size: 1.1em;"><b>{!! $addon['total'] > 0 ? '&euro;'. number_format($addon['total'], 2) : 'FREE' !!}</b></span>
                                                </div>
                                            </div>
                                            @if($step == 1)
                                                <a href="#" title="" class="text-muted remove-addon" data-id="{{$addon['id']}}"><i class="fal font-size-xl fa-times fa-fw"></i></a>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="{{count($sp_transfers) > 0 ? 'pt-2 mt-2 border-top-1' : ''}} border-alpha-grey transfer-container">
                                @if(count($sp_transfers) > 0)
                                    @foreach($sp_transfers as $transfer)
                                        <div class="d-flex justify-content-between align-items-end py-1 addon-{{$transfer['id']}}">
                                            <div class="mr-1 w-100">
                                                <div class="font-size-lg"><b>{{$transfer['name']}}</b></div>
                                                <div class="w-100 d-flex justify-content-between align-items-center">
                                                    <span>
                                                        {{$transfer['guests'] .' '. \Illuminate\Support\Str::plural('guest', $transfer['guests'])}}
                                                    </span>
                                                    <span class="text-custom" style="font-size: 1.1em;"><b>{!! $transfer['total'] > 0 ? '&euro;'. number_format($transfer['total'], 2) : 'FREE' !!}</b></span>
                                                </div>
                                            </div>
                                            @if($step == 1)
                                                <a href="#" title="" class="text-muted remove-transfer" data-id="{{$transfer['id']}}"><i class="fal font-size-xl fa-times fa-fw"></i></a>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="pt-2 mt-2 border-top-1 border-alpha-grey">
                                @if($pickup)
                                    <div class="d-flex justify-content-between align-items-center py-1 transfer-{{$pickup->id}}">
                                        <div class="mr-2">
                                            <div class="font-size-lg"><b>{{$pickup->name}}</b></div>
                                            <span>
                                                {{$guest .' '. \Illuminate\Support\Str::plural('guest', $guest)}}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                                @if($dropoff)
                                    <div class="d-flex justify-content-between align-items-center py-1 transfer-{{$dropoff->id}}">
                                        <div class="mr-2">
                                            <div class="font-size-lg"><b>{{$dropoff->name}}</b></div>
                                            <span>
                                                {{$guest .' '. \Illuminate\Support\Str::plural('guest', $guest)}}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <p>Leave a comment here if you have a specific request:</p>
                            <textarea name="comment" class="form-control form-control-sm" rows="5">{{ session('sp_comment') }}</textarea>
                            <div class="text-right">
                                <button class="btn btn-custom text-uppercase font-size-xs mt-2" id="btn-sp-comment">save</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="font-size-xl"><b>Total</b></div>
                                <span class="font-size-xl" id="grand-total">
                                    &euro;{{number_format($grand_total, 2)}}
                                </span>
                            </div>
                        </div>
                    </div>

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

                    <div class="card sidebar-stick" data-margin-top="94">
                        <div class="card-header bg-transparent text-center sidebar-title">
                            <b>Price Includes</b>
                        </div>
                        <div class="card-body camp-inclusions">
                            {!! $inclusion !!}
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
@endsection
