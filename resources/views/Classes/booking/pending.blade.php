@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
                <a href="{{ route('tenant.classes.bookings.index') }}" class="breadcrumb-item">Classes</a>
                <span class="breadcrumb-item active">Bookings</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper">
            <div class="content">
                <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                    <h4 class="m-0 mr-auto d-none d-md-block"><i class="fal fa-fw fa-calendar-alt mr-1"></i> All Pending Bookings</h4>
                    @can('add booking')
                        <a href="{{route('tenant.classes.bookings.calendar')}}" class="btn btn-labeled btn-labeled-left bg-warning-700 ml-1">
                            <b><i class="icon-plus3"></i></b> New Booking
                        </a>
                    @endcan
                </div>

                @if(request()->has('ref'))
                    <p>Booking total: <b>{{$bookings->count()}}</b></p>
                    <p>Pax total: <b>{{$bookings->sum(function ($b) { return $b->guests_count; })}}</b></p>
                @endif

                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-xs table-compact dark">
                            <thead>
                                <tr class="bg-grey-700">
                                    <th class="two wide">Ref</th>
                                    <th>Booker</th>
                                    <th class="text-center">Guests</th>
                                    <th class="text-center">Session</th>
                                    <th>Booked</th>
                                    <th>Price</th>
                                    <th class="text-center">With Pass</th>
                                    <th class="text-center">With Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td valign="center">
                                            <div class="d-flex justify-content-start align-items-center">
                                                <div class="circle {{ $booking->status_badge }} tippy" data-tippy-content="{{ $booking->status }}">&nbsp;</div>
                                                <div>
                                                    <a href="{{ route('tenant.classes.bookings.show', [ 'ref' => $booking->ref ]) }}" class="text-dark"><b>{{ $booking->ref }}</b></a>
                                                </div>
                                            </div>
                                        </td>
                                        <td><a href="{{ route('tenant.classes.guests.show', [ 'id' => $booking->guest->details->id ]) }}" title="" class="text-danger"><b>{{ $booking->guest->details->full_name }}</b></a></td>
                                        <td class="text-center">{{ $booking->people() }}</td>
                                        <td class="text-center">{{ $booking->sessions_count }}</td>
                                        <td>{{ $booking->booking_date->format('d.m.Y') }}</td>
                                        <td>
                                            @if(is_float($booking->payment->total) || is_int($booking->payment->total))
                                                <b>&euro;{{ $booking->payment->total }}</b>
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td class="text-center">{!! $booking->pass ? '<i class="fa fa-fw fa-check text-success"></i>' : '-' !!}</td>
                                        <td class="text-center">{!! $booking->pass?->type == 'CREDIT' ? '<i class="fa fa-fw fa-check text-success"></i>' : '-' !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
