@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
            <a href="{{ route('tenant.bookings') }}" class="breadcrumb-item">Bookings</a>
            <a href="{{ route('tenant.bookings.show', [ 'ref' => $booking->ref ]) }}" class="breadcrumb-item">#{{ $booking->ref }}</a>
            <span class="breadcrumb-item active">New Room</span>
        </div>
        
        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content new pt-4">
    
    @include('Booking.bookings.sidebar')
    
    <div class="content-wrapper container reset">
        <div class="content pt-0">
            <div class="card room-details">
                <div class="card-header bg-transparent header-elements-inline">
                    <h6 class="card-title"><i class="fa fa-bed mr-1"></i> <b>Edit {{ $guest->full_name }} room</b></h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                        </div>
                    </div>
                </div>
                <form action="javascript:" method="post" id="room-details" enctype="multipart/form-data">
                    <div class="card-body border-bottom-1 border-alpha-grey-300 p-0">
                        <table class="table table-xs">
                            <thead>
                                <tr class="bg-grey-700">
                                    <th>Room</th>
                                    <th>Bed</th>
                                    <th>Bathroom</th>
                                    <th>Check In - Check Out</th>
                                    <th>Duration / Qty</th>
                                    <th class="text-center">Guests</th>
                                    <th class="text-right">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="alpha-grey">
                                    <td>
                                        <i class="fa fa-bed fa-fw mr-1 tippy" data-tippy-content="Room"></i>
                                        <b>{{ $booking_room->subroom->name }}</b> {!! $booking_room->is_private ? '<i class="fa fa-fw fa-lock tippy" data-tippy-content="Private Booking"></i>' : '' !!}
                                    </td>
                                    <td>
                                        @if (count(json_decode($booking_room->room->bed_type, true)) > 1)
                                            <select class="form-control select-no-search form-control-sm" data-container-css-class="select-sm" data-fouc data-placeholder="Bed type" name="bed_type">
                                                @foreach (json_decode($booking_room->room->bed_type, true) as $bed)
                                                    <option value="{{ $bed }}" {{ $booking_room->bed_type == $bed ? 'selected' : '' }}>{{ $bed }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                        {{ $booking_room->bed_type }}
                                        <input type="hidden" name="bed_type" value="{{ $booking_room->bed_type }}" />
                                        @endif
                                    </td>
                                    <td>{{ $booking_room->bathroom }}</td>
                                    <td>
                                        {{ $booking_room->from->format('d.m.Y') }} <i class="icon-arrow-right5 mx-1 font-size-sm"></i> {{ $booking_room->to->format('d.m.Y') }}
                                    </td>
                                    <td>{{ $booking_room->days }} days / {{ $booking_room->nights }} nights</td>
                                    <td class="text-center">{{ $booking_room->guest }} <i class="far fa-user"></i></td>
                                    <td class="text-right">
                                        <div class="d-flex justify-content-end mr-3">
                                            <div class="input-group input-group-sm" style="width: 110px">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">&euro;</span>
                                                </span>
                                                <input type="text" name="price" class="form-control form-control-sm" id="current-room-price" value="{{ $booking_room->price }}" />
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @if ($booking_room->booking->location->duration_discount)
                                    <tr>
                                        <td colspan="6">
                                            <i class="fa fa-tag fa-fw mr-1 text-danger-300 tippy" data-tippy-content="Duration Discount"></i>
                                            Duration Discount
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end align-items-center mr-3">
                                                <div class="input-group input-group-sm" style="width: 110px">
                                                    <span class="input-group-prepend">
                                                        <span class="input-group-text">&euro;</span>
                                                    </span>
                                                    <input type="text" name="duration_discount" class="form-control form-control-sm" value="{{ floatVal($booking_room->duration_discount) }}" />
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @if ($booking_room->discounts())
                                    @foreach ($booking_room->discounts as $offer)
                                        <tr class="tr-offer-{{ $offer->id }}">
                                            <td colspan="6">
                                                <i class="fa fa-fw fa-dollar-sign mr-1 text-danger-300 tippy" data-tippy-content="Special Offer"></i> Special Offer:
                                                {{ $offer->offer->name }} ({!! $offer->offer->discount_type == 'Percent' ? $offer->offer->discount_value .'%' : '&euro;'. $offer->offer->discount_value !!})
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end align-items-center">
                                                    <div class="input-group input-group-sm" style="width: 110px">
                                                        <span class="input-group-prepend">
                                                            <span class="input-group-text">&euro;</span>
                                                        </span>
                                                        <input type="text" name="offer[{{ $offer->id }}]" class="form-control form-control-sm" value="{{ floatVal($offer->discount_value) }}" {{ $role == 4 ? 'readonly' : '' }} />
                                                    </div>
                                                    <a href="javascript:" title="" class="ml-1 text-danger remove-offer" data-id="{{ $offer->id }}"><i class="icon-cross2"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if ($booking_room->addons->count() > 0)
                                    @foreach ($booking_room->addons as $addon)
                                        <tr class="tr-addon-{{ $addon->id }}">
                                            <td colspan="4">
                                                <i class="fa fa-gift fa-fw mr-1 text-danger-300 tippy" data-tippy-content="Extra / Addon"></i> {{ $addon->details->name }}
                                                @if ($addon->info)
                                                    ({{ $addon->info }})
                                                @endif
                                            </td>
                                            <td class="text-left">
                                                @if ($addon->details->is_flexible)
                                                    {{ $addon->amount }} {{ $addon->details->unit_name }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center">
                                                    <input type="text" name="addon[{{ $addon->id }}][guests]" class="text-center form-control form-control-sm" value="{{ $addon->guests }}" style="width: 50px" />
                                                </div>
                                            </td>
                                            <td class="text-right">
                                                <div class="d-flex justify-content-end align-items-center">
                                                    <div class="input-group input-group-sm" style="width: 110px">
                                                        <span class="input-group-prepend">
                                                            <span class="input-group-text">&euro;</span>
                                                        </span>
                                                        <input type="text" name="addon[{{ $addon->id }}][price]" class="form-control form-control-sm" value="{{ $addon->price }}" />
                                                    </div>
                                                    <a href="javascript:" title="" class="ml-1 text-danger remove-addon" data-id="{{ $addon->id }}"><i class="icon-cross2"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="p-3 border-top-1 border-alpha-grey d-flex justify-content-end align-items-center">
                            @csrf
                            <a href="{{ route('tenant.bookings.show', ['ref' => $booking->ref]) }}" title="" class="text-muted mr-3">Return</a>
                            <input type="hidden" name="booking_guest_id" value="{{ $booking_guest_id }}" />
                            <input type="hidden" name="booking_room_id" value="{{ $booking_room_id }}" />
                            <input type="hidden" name="ref" id="ref" value="{{ $booking->ref }}" />
                            <input type="hidden" name="booking_id" id="booking_id" value="{{ $booking->id }}" />
                            <input type="hidden" id="booking_status" value="{{ $booking->status }}" />
                            <button type="submit" class="btn btn-outline bg-danger border-danger btn-sm btn-room-price rounded-round">Update</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h6 class="card-title"><i class="fa fa-cart-plus mr-1"></i> <b>Add-ons</b></h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                        </div>
                    </div>
                </div>
                <div class="card-body border-0">
                    <div class="text-uppercase font-size-lg mb-2"><b>Addons - Flexible</b></div>
                    @foreach ($addons->where('rate_type', 'Day')->all() as $addon)
                        <div class="d-flex justify-content-start align-items-center {{ $loop->last ? 'pt-2 pb-0' : 'border-bottom-1 py-2' }} border-alpha-grey">
                            <div style="width: 400px" class="text-uppercase mr-1">{{ $addon->name }}</div>
                            <div style="width: 130px" class="ml-auto ml-2">
                                <select
                                    class="form-control form-control-sm select-no-search addon-flexible-dd"
                                    data-container-css-class="select-sm"
                                    name="addon[{{ $addon->id }}][days]"
                                    id="addon_amount_{{ $addon->id }}"
                                    data-placeholder="Days"
                                    data-addon="{{ $addon->id }}"
                                    data-fouc
                                >
                                @for ($i = 1; $i <= $booking_room->nights; $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ Str::plural('day', $i) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div style="width: 150px" class="ml-2">
                            <div class="input-group input-group-sm tippy" data-tippy-content="Fill in to override the default value">
                                <span class="input-group-prepend">
                                    <span class="input-group-text">&euro;</span>
                                </span>
                                <input type="text" id="addon_price_{{ $addon->id }}" class="form-control form-control-sm" value="{{ $addon->base_price }}" />
                                <button class="btn btn-sm bg-grey-600 ml-1 addon-button" data-id="{{ $addon->id }}">
                                    <i class="font-size-sm icon-plus22"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="card-body">
                <div class="text-uppercase font-size-lg mb-2"><b>Addons - FIXED</b></div>
                @foreach ($addons->where('rate_type', 'Fixed')->all() as $addon)
                <div class="d-flex justify-content-start align-items-center {{ $loop->last ? 'pt-2 pb-0' : 'border-bottom-1 py-2' }} border-alpha-grey">
                    <div style="width: 400px" class="text-uppercase mr-1">{{ $addon->name }}</div>
                    @if ($addon->week_question)
                        <div style="width: 130px" class="ml-auto">
                            <select
                                class="form-control form-control-sm select-no-search addon-flexible-dd"
                                data-container-css-class="select-sm"
                                name="addon[{{ $addon->id }}][weeks]"
                                id="addon_weeks_{{ $addon->id }}"
                                data-placeholder="Weeks"
                                data-addon="{{ $addon->id }}"
                                data-fouc
                            >
                                @for ($i = 1; $i <= $weeks; $i++)
                                    <option value="{{ $i }}">
                                        {{ (new NumberFormatter('en_US', NumberFormatter::ORDINAL))->format(intVal($i)) }} week
                                    </option>
                                @endfor
                            </select>
                        </div>
                    @endif
                    <div style="width: 150px" class="{{ !$addon->week_question ? 'ml-auto' : 'ml-2' }}">
                        <div class="input-group input-group-sm tippy" data-tippy-content="Fill in to override the default value">
                            <span class="input-group-prepend">
                                <span class="input-group-text">&euro;</span>
                            </span>
                            <input type="hidden" id="addon_amount_{{ $addon->id }}" value="1" />
                            <input type="text" id="addon_price_{{ $addon->id }}" class="form-control form-control-sm" value="{{ $addon->base_price }}" />
                            <button class="btn btn-sm bg-grey-600 ml-1 addon-button" data-id="{{ $addon->id }}">
                                <i class="font-size-sm icon-plus22"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
        </div>
        
        <div class="card">
            <div class="card-header bg-transparent header-elements-inline">
                <h6 class="card-title"><i class="fa fa-file-import mr-1"></i> <b>Search & Replace room</b></h6>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                    </div>
                </div>
            </div>
            <div class="card-body border-0">
                <div class="row">
                    <div class="col-sm-4"><h6>Room Search</h6></div>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>* Stay</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                        </span>
                                        <input type="text" class="form-control daterange-basic daterange-basic-search" id="room-search-dates" value="{{ $booking_room->from->format('d.m.Y') .' - '. $booking_room->to->format('d.m.Y') }}"> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>* Location</label>
                                    <select class="form-control select" data-fouc data-placeholder="Location" name="location" {{ $booking->location_id ? 'disabled' : '' }}>
                                        <option></option>
                                        @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" {{ $booking->location_id == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="d-flex justify-content-end align-items-center">
                                    <div class="form-check form-check-inline mr-1">
                                        <label class="form-check-label">
                                            <span class="mr-2">Keep current room price</span>
                                            <input type="checkbox" id="keep-price" class="form-check-input-styled" checked data-fouc>
                                        </label>
                                    </div>
                                    <input type="hidden" id="guest_id" value="{{ $booking_guest_id }}" />
                                    <input type="hidden" id="booking_room_id" value="{{ $booking_room_id }}" />
                                    <input type="hidden" id="ref" value="{{ $booking->ref }}" />
                                    <input type="hidden" id="action" value="replace" />
                                    <button type="submit" class="btn bg-danger btn-room-search">Search Availability</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
        
        <div class="bg-transparent p-0 mt-2 search-container">
            <div id="search-result">
                <div class="text-center alpha-grey px-2 py-5">Search something first</div>
            </div>
        </div>
        
    </div>
</div>

</div>
@endsection

@section('scripts')
<script>
    window.IS_AGENT = {{ Auth::user()->hasRole('Agents') ? 1 : 0 }};
    window.bookingRef = '{{ $booking->ref }}';
    tippy('.tippy', {
        content: 'Tooltip',
        arrow: true,
    });
    $('.daterange-basic').daterangepicker({
        autoApply: true,
        locale: {
            format: 'DD.MM.YYYY'
        }
    });
</script>
@endsection
