@extends('Booking.app')

@section('content')

    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item"><a href="{{route('tenant.guests')}}" title="" class="text-grey">Guests</a></span>
                <span class="breadcrumb-item active">{{$guest->full_name}}</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                <div class="card">
                    <div class="card-header header-elements-inline bg-transparent">
                        <h4 class="card-title">
                            {{$guest->full_name}}
                        </h4>
                        <div class="header-elements">
                            <a href="{{route('tenant.guests')}}" title="" class="btn btn-link text-slate">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <p><b>CUSTOMER DETAILS</b></p>
                                <table width="100%" class="cust-table">
                                    <tr>
                                        <td width="40%">Client ID</td>
                                        <td width="60%" class="text-info">{{ $guest->client_number ?? '---' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Full Name</td>
                                        <td>{{$guest->full_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Company</td>
                                        <td>{{$guest->company != '' ? $guest->company : '---'}}</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td><a href="mailto:{{$guest->email}}" class="text-danger">{{$guest->email}}</a></td>
                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td>{{$guest->phone != '' ? $guest->phone : '---'}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-4">
                                <p><b>ADDRESS</b></p>
                                <table width="100%" class="cust-table">
                                    <tr>
                                        <td width="40%">Street</td>
                                        <td width="60%">{{$guest->street != '' ? $guest->street : '---'}}</td>
                                    </tr>
                                    <tr>
                                        <td>Zip Code</td>
                                        <td>{{$guest->zip}}</td>
                                    </tr>
                                    <tr>
                                        <td>City</td>
                                        <td>{{$guest->city != '' ? $guest->city : '---'}}</td>
                                    </tr>
                                    <tr>
                                        <td>Country</td>
                                        <td>{{$guest->country != '' ? $guest->country : '---'}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-4">
                                <p><b>OTHER</b></p>
                                <table width="100%" class="cust-table">
                                    <tr>
                                        <td width="40%">Total Bookings</td>
                                        <td width="60%">{{$guest->activeBookings->count()}}</td>
                                    </tr>
                                    <tr>
                                        <td>Marketing Consent</td>
                                        <td>{{$guest->marketing_flag == 1 ? 'Yes' : 'No'}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="pt-2 px-3">
                            <h5>Bookings</h5>
                        </div>

                        <table class="table table-xs table-compact dark">
                            <thead>
                                <tr class="bg-grey-700">
                                    <th>Ref</th>
                                    <th width="15%">Customer</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Guests</th>
                                    <th class="">Location</th>
                                    <th>Booked</th>
                                    <th>Check In / Out</th>
                                    <th>Price</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($guest->bookings as $b)
                                    @if($b->booking?->guest)
                                        <tr>
                                            <td>
                                                <a href="{{route('tenant.bookings.show', [ 'ref' => $b->booking->ref ])}}" class="text-danger"><b>{{$b->booking->ref}}</b></a>
                                                {!! $b->booking->special_package_id ? '<span class="tippy" data-tippy-content="'. $b->booking->specialPackage->name .'"><b>SP</b></span>' : '' !!}
                                                {!! $b->booking->smoobu_id ? '<span class="tippy" data-tippy-content="Connected to Smoobu ('. $b->booking->smoobu_id .')"><b>S</b></span>' : '' !!}
                                            </td>
                                            <td><a href="{{route('tenant.guests.show', [ 'id' => $b->booking->guest->details->id ])}}" title="" class="text-danger"><b>{{$b->booking->guest->details->full_name}}</b></a></td>
                                            <td class="text-center">
                                                <span class="badge {{$b->booking->status_badge}} badge-pill">{{$b->booking->booking_status}}</span>
                                            </td>
                                            <td class="text-center">{{$b->booking->guests->count()}}</td>
                                            <td>
                                                <span {!! $b->booking->location_id != '' ? 'class="tippy" data-tippy-content="'. $b->booking->getAllRoomsName() .'"' : '' !!}><b>{{$b->booking->location->short_name}}</b></span>
                                            </td>
                                            <td>{{$b->booking->created_at->format('d.m.Y H:i:s')}}</td>
                                            <td><b>{{date('d.m.Y', strtotime($b->booking->check_in))}}</b> &mdash; <b>{{date('d.m.Y', strtotime($b->booking->check_out))}}</b></td>
                                            <td><b>&euro;{{floatVal(round($b->booking->payment->total, 2))}}</b></td>
                                            <td>
                                                @can('delete setting')
                                                    <a href="{{route('tenant.bookings.deleteBooking', [ 'ref' => $b->booking->ref ])}}?guest= {{$guest->id}}" title="" class="text-danger confirm-dialog" data-text="Delete this booking?"><i class="fal fa-fw fa-times"></i></a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
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
