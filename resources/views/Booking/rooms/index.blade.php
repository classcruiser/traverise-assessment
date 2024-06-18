@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item active">Room Categories</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-bed mr-1"></i> Room Categories</h4>
                <form action="{{ route('tenant.rooms') }}" method="get">
                    <div class="d-flex justify-content-start align-items-center mr-2">
                        <div style="width: 200px" class="ml-auto">
                            <select name="camp" class="form-control">
                                <option>All camps</option>
                                @foreach ($all_camps as $camp)
                                    <option value="{{ $camp->id }}" {{ request()->has('camp') && request('camp') == $camp->id ? 'selected' : '' }}>{{ $camp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @csrf
                        <button class="btn bg-danger ml-1">
                            Submit
                        </button>
                    </div>
                </form>
                @can ('add room')
                    <a href="/rooms/new" title="" class="btn bg-danger">
                        <i class="far fa-plus mr-1"></i> New Room Category
                    </a>
                @endcan
            </div>

            <div class="card">
                <table class="table table-xs table-compact sortable" data-url="{{ route('tenant.rooms.sort') }}">
                    <thead>
                        <tr class="bg-grey-700">
                            <th></th>
                            <th>Name</th>
                            <th>Active</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Default price</th>
                            <th>Rooms count</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($camps as $camp)
                            <tr>
                                <td colspan="8" class="bg-grey-100 text-uppercase font-weight-bold p-0">
                                    <a href="#" class="toggle-row text-body d-block border-bottom px-2 py-2" data-target="row-{{ $camp->id }}" title="">
                                        <i id="row-{{ $camp->id }}" class="fa fa-angle-down mr-1 pl-1"></i> {{ $camp->name }}
                                    </a>
                                </td>
                            </tr>
                            @foreach ($camp->rooms as $room)
                                <?php $total_rooms = count($room->rooms); ?>
                                <tr class="row-{{ $camp->id }}" data-id="{{ $room->id }}">
                                    <td class="text-center"><span class="handler cursor-move"><i class="fal fa-bars fa-fw"></i></span></td>
                                    <td class="vertical-top"><b><a href="/rooms/{{ $room->id }}#room-details" class="list-icons-item text-danger">{{ $room->name }}</a></b></td>
                                    <td class="text-center">{!! $room->active ? '<i class="far fa-fw fa-check text-success"></i>' : '<i class="fal fa-fw fa-times text-danger"></i>' !!}</td>
                                    <td>{{ $room->room_type }}</td>
                                    <td>{{ $room->location->name }}</td>
                                    <td><b>&euro;{{ $room->default_price }}</b></td>
                                    <td>{{ $total_rooms }} {{ Str::plural('Room', $total_rooms) }}</td>
                                    <td class="text-right">
                                        <div class="list-icons">
                                            <a href="/rooms/{{ $room->id }}#room-details" class="list-icons-item text-danger"><i class="icon-pencil7"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>

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
    })
    $('.toggle-row').on('click', function (e) {
        e.preventDefault();
        const target = $(this).data('target');

        $('.' + target).toggle();
        if ($('#' + target).hasClass('fa-angle-right')) {
            $('#' + target).removeClass('fa-angle-right').addClass('fa-angle-down');
        } else {
            $('#' + target).removeClass('fa-angle-down').addClass('fa-angle-right');
        }
    });
</script>
@endsection