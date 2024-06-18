@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
                <a href="{{route('tenant.bookings')}}" class="breadcrumb-item">Bookings</a>
                <a href="{{route('tenant.bookings.show', [ 'ref' => $booking->ref ])}}" class="breadcrumb-item">#{{$booking->ref}}</a>
                <span class="breadcrumb-item active">Edit Guest</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content new pt-4">

        @include('Booking.bookings.sidebar')

        <div class="content-wrapper container reset">
            <div class="content pt-0">
                <div class="card">
                    <div class="card-header bg-transparent header-elements-inline">
                        <h6 class="card-title"><i class="fa fa-user mr-1"></i> <b>Edit guest</b></h6>
                        <div class="header-elements">
                            Or replace with <a href="#collapse-search" title="" class="btn btn-sm bg-danger ml-2" data-toggle="collapse"><i class="far fa-search mr-1"></i> Existing guest</a>
                        </div>
                    </div>
                    <form action="{{ route('tenant.bookings.editGuest', [ 'ref' => $booking->ref, 'booking_guest_id' => $booking_guest_id ]) }}" method="post">
                        <div class="card-body alpha-grey2 text-center collapse" id="collapse-search">
                            <select class="form-control select-remote-data" name="guest-search" data-fouc data-placeholder="Search guest...">
                                <option></option>
                            </select>
                            <button class="btn bg-slate btn-sm ml-1 replace-guest"><i class="fal fa-file-import mr-1"></i> Replace</button>
                            <input type="hidden" id="guest_id" value="{{$booking_guest_id}}" />
                            <input type="hidden" id="ref" value="{{$booking->ref}}" />
                        </div>
                        <div class="card-body border-0">
                            @include('Booking.partials.form-messages')
                            @include('Booking.partials.form-errors')
                            <div class="row">
                                <div class="col-sm-4">
                                    <h6>Guest Details</h6>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>* First Name</label>
                                                <input type="text" name="fname" placeholder="First name" class="form-control" value="{{$guest->fname}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>* Last Name</label>
                                                <input type="text" name="lname" placeholder="Last name" class="form-control" value="{{$guest->lname}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 hidden">
                                            <div class="form-group">
                                                <label>* Honorific</label>
                                                <select class="form-control select" name="title" data-fouc data-placeholder="Honorific">
                                                    <option></option>
                                                    <option value="Mr" {{$guest->title == 'Mr' ? 'selected' : ''}}>Mr</option>
                                                    <option value="Mrs" {{$guest->title == 'Mrs' ? 'selected' : ''}}>Mrs</option>
                                                    <option value="Ms" {{$guest->title == 'Ms' ? 'selected' : ''}}>Ms</option>
                                                    <option value="Divers" {{$guest->title == 'Divers' ? 'selected' : ''}}>Divers</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Company</label>
                                                <input type="text" name="company" placeholder="Company" class="form-control" value="{{$guest->company}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>* Email</label>
                                                <input type="text" name="email" placeholder="Email" class="form-control" value="{{$guest->email}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="text" name="phone" placeholder="Phone number" class="form-control" value="{{$guest->phone}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Birthdate</label>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <input type="text" name="birthdate_day" placeholder="Day" class="form-control mr-3" style="width:60px" value="{{$birthdate[2]}}">
                                                    <select class="form-control" name="birthdate_month">
                                                        <option>Month</option>
                                                        <option value="01" {{$birthdate[1] == '01' ? 'selected' : ''}}>January</option>
                                                        <option value="02" {{$birthdate[1] == '02' ? 'selected' : ''}}>February</option>
                                                        <option value="03" {{$birthdate[1] == '03' ? 'selected' : ''}}>March</option>
                                                        <option value="04" {{$birthdate[1] == '04' ? 'selected' : ''}}>April</option>
                                                        <option value="05" {{$birthdate[1] == '05' ? 'selected' : ''}}>May</option>
                                                        <option value="06" {{$birthdate[1] == '06' ? 'selected' : ''}}>June</option>
                                                        <option value="07" {{$birthdate[1] == '07' ? 'selected' : ''}}>July</option>
                                                        <option value="08" {{$birthdate[1] == '08' ? 'selected' : ''}}>August</option>
                                                        <option value="09" {{$birthdate[1] == '09' ? 'selected' : ''}}>September</option>
                                                        <option value="10" {{$birthdate[1] == '10' ? 'selected' : ''}}>October</option>
                                                        <option value="11" {{$birthdate[1] == '11' ? 'selected' : ''}}>November</option>
                                                        <option value="12" {{$birthdate[1] == '12' ? 'selected' : ''}}>December</option>
                                                    </select>
                                                    <input type="text" name="birthdate_year" placeholder="Year" class="ml-3 form-control" style="width:70px" value="{{$birthdate[0]}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <h6>Address</h6>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Street</label>
                                                <input type="text" name="street" placeholder="Street" class="form-control" value="{{$guest->street}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>City</label>
                                                <input type="text" name="city" placeholder="City" class="form-control" value="{{$guest->city}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Zip Code</label>
                                                <input type="text" name="zip" placeholder="Zip Code" class="form-control" value="{{$guest->zip}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>* Country</label>
                                                <select class="form-control select" data-fouc name="country" data-placeholder="Country">
                                                    @foreach($countries as $country)
                                                        <option value="{{$country->country_name}}">{{$country->country_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <h6>Agent</h6>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Agent</label>
                                                <select class="form-control" name="agent_id" data-placeholder="Agent" {{auth()->user()->hasRole('Agent') ? 'disabled readonly' : ''}}>
                                                    <option value="">No Agent</option>
                                                    @foreach($agents as $agent)
                                                        <option value="{{$agent->id}}" {{$agent->id == $guest->agent_id ? 'selected' : ''}}>{{$agent->name}}</option>
                                                    @endforeach
                                                </select>
                                                @hasrole('Agent')
                                                    <input type="hidden" name="agent_id" value="{{$guest->agent_id}}" />
                                                @endhasrole
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @can('edit booking')
                            <div class="card-body">
                                <div class="d-flex justify-content-end align-items-center">
                                    @csrf
                                    <input type="hidden" name="guest_id" value="{{$guest->id}}" />
                                    <a href="{{route('tenant.bookings.show', ['ref' => $booking->ref])}}" title="" class="text-muted mr-3">Return</a>
                                    <button type="submit" class="btn bg-danger btn-lg">Submit</button>
                                </div>
                            </div>
                        @endcan

                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
    var title = '{{$guest->title}}';
    var month = '{{$birthdate[1]}}';
    var country = '{{$guest->country}}';
    var selectedBoard = "{!! $guest->selected_board !!}";

    $(document).ready(function() {
        $('.select[name="title"]').val(title);
        $('.select[name="birthdate_month"]').val(month);
        $('.select[name="country"]').val(country);
        $('.select').trigger('change');
    });
    </script>
@endsection
