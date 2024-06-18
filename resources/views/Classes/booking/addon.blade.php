@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
                <a href="{{ route('tenant.classes.bookings.index') }}" class="breadcrumb-item">Classes</a>
                <a href="{{ route('tenant.classes.bookings.show', ['ref' => $booking->ref]) }}" class="breadcrumb-item"># {{ $booking->ref }}</a>
                <span class="breadcrumb-item active">Addons</span>
            </div>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content new pt-4">
        @include('Classes.booking.sidebar')

        <div class="content-wrapper container reset">
            <div class="content pt-0">
                <div class="card booking-details">
                    <div class="card-header alpha-grey header-elements-inline">
                        <h6 class="card-title"><i class="fa fa-clipboard mr-1"></i> <b>Booking Addons</b></h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item rotate-180" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('tenant.classes.bookings.update_price', ['ref' => $booking->ref]) }}" method="POST" id="session-details">
                        <table class="table table-xs new">
                            <thead>
                                <tr class="bg-grey-700">
                                    <th class="w-50">Addon</th>
                                    <th>Unit</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-right">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($booking->addons as $addon)
                                    <tr class="tr-addon-{{ $addon->id }}">
                                        <td>
                                            <i class="fa fa-layer-group fa-fw mr-1 text-danger-300 tippy" data-tippy-content="Extra / Addon"></i> {{$addon->addon->name}}
                                        </td>
                                        <td class="text-left">
                                            @if($addon->addon->rate_type == 'Day')
                                                {{intVal($addon->amount)}} {{$addon->addon->unit_name}}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{$addon->amount}} <i class="far fa-user"></i>
                                        </td>
                                        <td class="text-right">
                                            @can('see prices')
                                                <div class="d-flex justify-content-end align-items-center">
                                                    <div class="input-group input-group-sm" style="width: 110px">
                                                        <span class="input-group-prepend">
                                                            <span class="input-group-text">&euro;</span>
                                                        </span>
                                                        <input type="text" name="addons[{{$addon->id}}][price]" class="form-control form-control-sm" value="{{ $addon->price }}" {{!auth()->user()->can('edit prices') || $is_deleted ? 'readonly' : ''}} />
                                                    </div>
                                                    <a href="javascript:void(0)" title="" class="ml-2 text-danger tippy remove-session-addon" data-id="{{ $addon->id }}" data-url="{{ route('tenant.classes.bookings.addons.destroy', ['id' => $addon->id, 'ref' => $booking->ref]) }}" data-tippy-content="Delete addon"><i class="icon-cross2"></i></a>
                                                </div>
                                            @else
                                                --
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No addons added</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if($booking->guests_count > 0 && !$is_deleted)
                            @can('edit prices')
                                <div class="border-top-1 border-alpha-grey d-flex justify-content-end align-items-center p-3">
                                    @csrf
                                    <a href="{{ route('tenant.classes.bookings.show', ['ref' => $booking->ref]) }}" class="text-muted mr-3">Return</a>
                                    @if ($booking->addons->count() > 0)
                                    <button class="btn btn-outline border-danger bg-danger btn-sm btn-session-price rounded-round" type="submit">Update</button>
                                    @endif
                                </div>
                            @endcan
                        @endif
                    </form>
                </div>

                <div class="card">
                    <div class="card-header bg-transparent header-elements-inline">
                        <h6 class="card-title"><i class="fa fa-layer-plus mr-1"></i> <b>Add-ons</b></h6>
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
                                <div style="width: 150px" class="ml-auto">
                                    <div class="input-group input-group-sm tippy" data-tippy-content="Fill in to override the default value">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text">&euro;</span>
                                        </span>
                                        <input type="text" id="addon_price_{{ $addon->id }}" class="form-control form-control-sm" value="{{ $addon->base_price }}" />
                                        <button class="btn btn-sm bg-grey-600 ml-1 addon-session-button" data-id="{{ $addon->id }}" data-url="{{ route('tenant.classes.bookings.addons.store', ['ref' => $booking->ref]) }}">
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
                                <div style="width: 150px" class="ml-auto">
                                    <div class="input-group input-group-sm tippy" data-tippy-content="Fill in to override the default value">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text">&euro;</span>
                                        </span>
                                        <input type="text" id="addon_price_{{ $addon->id }}" class="form-control form-control-sm" value="{{ $addon->base_price }}" />
                                        <button class="btn btn-sm bg-grey-600 ml-1 addon-session-button" data-id="{{ $addon->id }}" data-url="{{ route('tenant.classes.bookings.addons.store', ['ref' => $booking->ref]) }}">
                                            <i class="font-size-sm icon-plus22"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    tippy('.tippy', {
        content: 'Tooltip',
        arrow: true,
    });
</script>
@endsection
