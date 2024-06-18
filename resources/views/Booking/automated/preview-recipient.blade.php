@extends('Booking.app') 

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <span class="breadcrumb-item"><a href="{{ route('tenant.automated-emails') }}" title="" class="text-grey">Automated Emails</a></span>
                <span class="breadcrumb-item active">Preview Recipient</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                    <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-envelope-open mr-1"></i> Preview Recipient</h4>
                </div>
                <div class="card">
                    <table class="table table-xs table-compact">
                        <thead>
                            <tr class="bg-grey-700">
                                <th>REF</th>
                                <th>GUEST</th>
                                <th>PAX</th>
                                <th>SUBMITTED</th>
                                <th>CHECK IN</th>
                                <th>TOTAL</th>
                                <th>PAID</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="circle {{$booking->status_badge}}">&nbsp;</div>
                                            <div>
                                                <a href="{{route('tenant.bookings.show', [ 'ref' => $booking->ref ])}}" class="{{$booking->is_blacklisted ? 'text-grey-300' : 'text-dark'}}"><b>{{$booking->ref}}</b></a>
                                                {!! $booking->is_blacklisted ? '<span class="tippy" data-tippy-content="Contains blacklisted guest"><i class="fa fa-ban text-danger"></i></span>' : '' !!}
                                                {!! $booking->special_package_id ? '<span class="tippy" data-tippy-content="'. $booking->specialPackage->name .'"><b>SP</b></span>' : '' !!}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{route('tenant.guests.show', [ 'id' => $booking->guest->details->id ])}}" title="" class="text-danger"><b>{{$booking->guest->details->full_name}}</b></a>
                                    </td>
                                    <td>{{$booking->guests_count}}</td>
                                    <td><b>{{date('d.m.Y', strtotime($booking->created_at))}}</b></td>
                                    <td><b>{{date('D d.m.Y', strtotime($booking->check_in))}}</b></td>
                                    <td>
                                        @if(is_float($booking->payment->total) || is_int($booking->payment->total))
                                            <b>&euro;{{$booking->parsePrice($booking->payment->total)}}</b>
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-{{$booking->status_badge}}">
                                            <b>&euro;{{$booking->parsePrice($booking->payment->total_paid)}}</b>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('tenant.automated-emails.exclude-bookings', ['id' => $id, 'booking_id' => $booking->id]) }}" title="" class="text-danger tippy" data-tippy-content="Exclude this booking from this specific email"><i class="far fa-ban fa-fw"></i></a>
                                    </td>
                                </tr>
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
    </script>
@endsection