@extends('Booking.app')

@section('content')

<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item"><a href="{{ route('tenant.classes.bookings.index') }}" title="" class="text-grey">Bookings</a></span>
            <span class="breadcrumb-item active">New Class Booking</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h4 class="card-title">Select Class</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.classes.bookings.index') }}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <iframe src="/classes/bookings/calendar-only?next=/classes/bookings/create" style="width: 100%; height: 80vh; border: 0;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection