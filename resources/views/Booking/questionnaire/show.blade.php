@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <a href="{{ route('tenant.questionnaire') }}" title="" class="breadcrumb-item">Addons</a>
                <span class="breadcrumb-item active">Edit questionnaire</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content" id="questionnaire">
        <div class="content-wrapper container">
            <div class="content">
                @if(session()->has('messages'))
                    <div class="alert bg-green-400 text-white alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <i class="fa fa-check-circle mr-1"></i> {{session('messages')}}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header bg-transparent header-elements-inline">
                        <h4 class="card-title">Edit questionnaire</h4>
                        <div class="header-elements">
                            <a href="{{route('tenant.questionnaire')}}" title="" class="btn btn-link text-danger">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>

                    <Questionnaire :questionnaire="{{$questionnaire->toJson()}}" :types="{{$types->toJson()}}" />
                </div>
            </div>
        </div>
    </div>

@endsection
