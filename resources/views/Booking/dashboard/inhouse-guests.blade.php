@extends('app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="row">
                <div class="col-12" id="arriving-guest-app">
                    <h4 class="m-0 mr-auto mb-2">In-house Guests {{ $location->name }} {{ $today->format('d M Y') }}</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body py-0 px-0">
                            <div class="table-responsive">
                                <table class="table table-xs table-compact">
                                    <thead>
                                        <tr class="bg-grey-700">
                                            <th class="two wide">Guest Name</th>
                                            <th class="text-left">Room</th>
                                            <th class="text-right">Check In</th>
                                            <th class="text-right">Check Out</th>
                                            <th class="text-right">REF</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($bookings as $guest)
                                            <tr>
                                                <td>{{ $guest->details->details->full_name }}</td>
                                                <td>{{ $guest->room->subroom->name }}</td>
                                                <td class="text-right">{{ $guest->room->from->format('d.m.Y') }}</td>
                                                <td class="text-right">{{ $guest->room->to->format('d.m.Y') }}</td>
                                                <td class="text-right">
                                                    <a href="/bookings/{{ $guest->room->booking->ref }}" title="" class="bold" style="font-family: monospace;">#{{ $guest->room->booking->ref }}</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2">No guests yet</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
    })
</script>
@endsection
