@extends('Booking.app')

@section('content')

<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item active">Session Calendar</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper">
        <div class="content">
            <div class="card">
                <div class="card-body border-0 p-0">
                    <iframe src="{{route('tenant.classes.calendarOnly', ['footer' => false, 'category' => $category_id])}}" style="width: 100%; height: 88vh; border: 0;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
