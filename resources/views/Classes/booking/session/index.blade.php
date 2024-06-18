@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
                <a href="{{ route('tenant.classes.bookings.index') }}" class="breadcrumb-item">Classes</a>
                <a href="{{ route('tenant.classes.bookings.show', ['ref' => $booking->ref]) }}" class="breadcrumb-item"># {{ $booking->ref }}</a>
                <span class="breadcrumb-item active">Session</span>
            </div>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content new pt-4">
        @include('Classes.booking.sidebar')

        <div class="content-wrapper container reset">
            <div class="content pt-0">
                <div class="card">
                    <div class="card-header bg-transparent header-elements-inline">
                        <h4 class="card-title">Select Class</h4>
                        <div class="header-elements">
                            <a href="{{ route('tenant.classes.bookings.show', ['ref' => $booking->ref]) }}" title="" class="btn btn-link text-danger">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>
                    <div class="card-body border-0 p-0">
                        <iframe src="/classes/bookings/calendar-only?next={{ route('tenant.classes.bookings.sessions.create', ['ref' => $booking->ref]) }}" style="width: 100%; height: 80vh; border: 0;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
