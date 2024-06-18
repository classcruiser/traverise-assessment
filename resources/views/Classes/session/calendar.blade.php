@extends('Booking.app_vue')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <span class="breadcrumb-item"><a href="{{ route('tenant.classes.sessions.index') }}" title="" class="text-grey">Class Sessions</a></span>
                <span class="breadcrumb-item active">{{ count($category->sessions) > 1 ? $category->name : $category->sessions[0]->name }} Calendar</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container-fluid">
            <div class="content">
                <div class="card">
                    <div class="card-header bg-transparent header-elements-inline">
                        <h4 class="card-title">{{ count($category->sessions) > 1 ? $category->name : $category->sessions[0]->name }} Calendar</h4>
                        <div class="header-elements">
                            <a href="{{ route('tenant.classes.sessions.calendar', ['catId' => $category->id]) }}" title="" class="btn btn-link text-danger">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>

                    <div class="card-body border-0 p-0">
                        <sessions-calendar :category="{{json_encode($category->toArray())}}" />
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
