@extends('Booking.app') 

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item active">Pending Bookings</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper">
            <div class="content">
                <h4 class="mb-3"><i class="fal fa-fw fa-calendar-alt mr-1"></i> Pending Bookings</h4>

                <div class="card">
                    <table class="table table-xs table-compact dark">
                        <thead>
                            <tr class="bg-grey-700">
                                <th class="two wide">Ref</th>
                                <th>Customer</th>
                                <th class="one wide text-center">Guests</th>
                                <th class="two wide">Location</th>
                                <th>Booked</th>
                                <th>Check In / Out</th>
                                <th>Price</th>
                                <th class="text-center">Commission</th>
                                <th class="text-center">Channel</th>
                                <th class="text-left">Opportunity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td valign="center">
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="circle {{$booking->status_badge}}">&nbsp;</div>
                                            <div>
                                                <a href="{{route('tenant.bookings.show', [ 'ref' => $booking->ref ])}}" class="{{$booking->is_blacklisted ? 'text-grey-300' : 'text-dark'}}"><b>{{$booking->ref}}</b></a>
                                                {!! $booking->is_blacklisted ? '<span class="tippy" data-tippy-content="Contains blacklisted guest"><i class="fa fa-ban text-danger"></i></span>' : '' !!}
                                                {!! $booking->special_package_id ? '<span class="tippy" data-tippy-content="'. $booking->specialPackage->name .'"><b>SP</b></span>' : '' !!}
                                            </div>
                                        </div>
                                    </td>
                                    <td><a href="{{Route('tenant.guests.show', [ 'id' => $booking->guest->details->id ])}}" title="" class="text-danger"><b>{{$booking->guest->details->full_name}}</b></a></td>
                                    <td class="text-center">{{$booking->guests_count}}</td>
                                    <td>
                                        <span class="tippy" data-tippy-content="{{$booking->getAllRoomsName()}}"><b>{{$booking->location->abbr}}</b></span>
                                    </td>
                                    <td>{{$booking->created_at->format('d.m.Y H:i:s')}}</td>
                                    <td><b>{{date('d.m.Y', strtotime($booking->check_in))}}</b> &mdash; <b>{{date('d.m.Y', strtotime($booking->check_out))}}</b></td>
                                    <td><b>&euro;{{floatVal(round($booking->grand_total, 2))}}</b></td>
                                    <td class="text-center">&euro;{{$booking->commission}}</td>
                                    <td class="text-center">{{$booking->channel}}</td>
                                    <td class="text-let">{{$booking->opportunity}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>{{$bookings->appends($_GET)->links()}}</div>
                    <a href="#" title="" class="btn btn-success">
                        <i class="fa fa-fw fa-file-excel"></i> Export to Excel
                    </a>
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