@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item active">Draft Bookings</span>
        </div>
        
        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper">
        <div class="content">
            <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-calendar-alt mr-1"></i> Draft Bookings</h4>
                <a href="/bookings/new" class="btn btn-labeled btn-labeled-left bg-warning-700 ml-1">
                    <b><i class="icon-plus3"></i></b> New Booking
                </a>
            </div>
            
            <div class="card">
                <table class="table table-xs table-compact dark">
                    <thead>
                        <tr class="bg-grey-700">
                            <th class="two wide">Ref</th>
                            <th>Customer</th>
                            <th class="one wide">Status</th>
                            <th class="one wide text-center">Guests</th>
                            <th class="two wide">Location</th>
                            <th>Booked</th>
                            <th>Check In / Out</th>
                            <th>Price</th>
                            <th class="text-center">Commission</th>
                            <th class="text-right">Expired</th>
                            <th class="text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                        <tr>
                            <td><a href="{{ route('tenant.bookings.show', [ 'ref' => $booking->ref ]) }}" class="text-danger"><b>{{ $booking->ref }}</b></a></td>
                            <td>
                                @if ($booking->guest)
                                    <a href="/guests/{{ $booking->guest->details->id }}" title="" class="text-danger"><b>{{ $booking->guest->details->full_name }}</b></a>
                                @else
                                    ---
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $booking->status_badge }} badge-pill">{{ $booking->booking_status }}</span>
                            </td>
                            <td class="text-center">{{ $booking->guests_count <= 0 ? '---' : $booking->guests_count }}</td>
                            <td>
                                @if ($booking->location_id)
                                    <span {{ $booking->getAllRoomsName() != '' ? 'class="tippy"' : '' }} data-tippy-content="{{ $booking->getAllRoomsName() }}"><b>{{ $booking->location->short_name ?? '-' }}</b></span>
                                @else
                                    <span>---</span>
                                @endif
                            </td>
                            <td>{{ $booking->created_at->format('d.m.Y H:i:s') }}</td>
                            <td>
                                @if (!is_null($booking->check_in))
                                    <b>{{ date('d.m.Y', strtotime($booking->check_in)) }}</b> &mdash; <b>{{ date('d.m.Y', strtotime($booking->check_out)) }}</b>
                                @else
                                    ---
                                @endif
                            </td>
                            <td><b>&euro;{{ floatVal(round($booking->grand_total, 2)) }}</b></td>
                            <td class="text-center">&euro;{{ $booking->commission }}</td>
                            <td class="text-right">{{ $booking->expiry->format('d.m.Y H:i') }}</td>
                            <td class="text-right">
                                @can ('delete booking')
                                <a href="/bookings/{{ $booking->ref }}/delete" title="" class="btn btn-danger btn-xs confirm-dialog" data-text="Delete this draft booking?"><i class="fal fa-fw fa-times"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div>{{ $bookings->appends($_GET)->links() }}</div>
                <!--<a href="#" title="" class="btn btn-success">
                    <i class="fa fa-fw fa-file-excel"></i> Export to Excel
                </a>-->
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
