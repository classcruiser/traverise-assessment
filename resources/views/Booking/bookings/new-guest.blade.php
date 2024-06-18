@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
                <a href="{{route('tenant.bookings')}}" class="breadcrumb-item">Bookings</a>
                <a href="{{route('tenant.bookings.show', [ 'ref' => $booking->ref ])}}" class="breadcrumb-item">#{{$booking->ref}}</a>
                <span class="breadcrumb-item active">Add Guest</span>
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
                        <h6 class="card-title"><i class="fa fa-user mr-1"></i> <b>Add new guest</b></h6>
                        <div class="header-elements">
                            OR <a href="#collapse-search" title="" class="btn btn-sm bg-danger ml-2" data-toggle="collapse"><i class="far fa-search mr-1"></i> Search existing guest</a>
                        </div>
                    </div>
                    <form action="{{route('tenant.bookings.insertGuest', [ 'ref' => $booking->ref ])}}" method="post">
                        <div class="card-body alpha-grey2 text-center collapse" id="collapse-search">
                            <select class="form-control select-remote-data" name="guest-search" data-fouc data-placeholder="Search guest...">
                                <option></option>
                            </select>
                            <button class="btn bg-danger btn-sm ml-1 add-guest"><i class="fal fa-plus mr-1"></i> Add</button>
                            <input type="hidden" id="ref" value="{{$booking->ref}}" />
                        </div>
                        <div class="card-body border-0">
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
                                                <input type="text" name="fname" placeholder="First name" class="form-control" value="{{old('fname')}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>* Last Name</label>
                                                <input type="text" name="lname" placeholder="Last name" class="form-control" value="{{old('lname')}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 hidden">
                                            <div class="form-group">
                                                <label>* Honorific</label>
                                                <select class="form-control select" data-fouc data-placeholder="Honorific" name="title">
                                                    <option></option>
                                                    <option value="Mr">Mr</option>
                                                    <option value="Mrs">Mrs</option>
                                                    <option value="Ms">Ms</option>
                                                    <option value="Divers">Divers</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Company</label>
                                                <input type="text" name="company" placeholder="Company" class="form-control" value="{{old('company')}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>* Email</label>
                                                <input type="text" name="email" placeholder="Email" class="form-control" value="{{ old('email') }}" {{ auth()->user()->hasRole('Agent') ? 'readonly' : ''}}>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="text" name="phone" placeholder="Phone number" class="form-control" value="{{old('phone')}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Birthdate</label>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <input type="text" name="birthdate_day" placeholder="Day" class="form-control mr-3" style="width:60px" value="{{old('birthdate_day')}}">
                                                    <select class="form-control select" data-fouc data-placeholder="Month" name="birthdate_month">
                                                        <option></option>
                                                        <option value="01">January</option>
                                                        <option value="02">February</option>
                                                        <option value="03">March</option>
                                                        <option value="04">April</option>
                                                        <option value="05">May</option>
                                                        <option value="06">June</option>
                                                        <option value="07">July</option>
                                                        <option value="08">August</option>
                                                        <option value="09">September</option>
                                                        <option value="10">October</option>
                                                        <option value="11">November</option>
                                                        <option value="12">December</option>
                                                    </select>
                                                    <input type="text" name="birthdate_year" placeholder="Year" class="ml-3 form-control" style="width:70px" value="{{old('birthdate_year')}}">
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
                                                <input type="text" name="street" placeholder="Street" class="form-control" value="{{old('street')}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>City</label>
                                                <input type="text" name="city" placeholder="City" class="form-control" value="{{old('city')}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Zip Code</label>
                                                <input type="text" name="zip" placeholder="Zip Code" class="form-control" value="{{old('zip')}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>* Country</label>
                                                <select class="form-control select" data-fouc name="country" data-placeholder="Country">
                                                    <option></option>
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
                                                <select class="form-control" name="agent_id" data-placeholder="Agent" {{auth()->user()->hasRole('Agent') ? 'readonly disabled' : ''}}>
                                                    <option value="">No Agent</option>
                                                    @foreach($agents as $agent)
                                                        <option value="{{$agent->id}}" {{auth()->user()->hasRole('Agent') ? (auth()->user()->id == $agent->id ? 'selected' : '') : ''}}>{{$agent->name}}</option>
                                                    @endforeach
                                                </select>
                                                @hasrole('Agent')
                                                    <input type="hidden" name="agent_id" value="{{auth()->user()->id}}" />
                                                @endhasrole
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @can('add booking')
                            <div class="card-body">
                                <div class="d-flex justify-content-end align-items-center">
                                    @csrf
                                    <input type="hidden" name="is_agent" value="{{ auth()->user()->hasRole('Agent') ? 'on' : '' }}" />
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
    var honorific = '{{old('title')}}';
    var month = '{{old('birthdate_month')}}';
    var country = '{{old('country')}}';

    $(document).ready(function() {
        $('.select[name="title"]').val(honorific);
        $('.select[name="birthdate_month"]').val(month);
        $('.select[name="country"]').val(country);
        $('.select').trigger('change');
    });
    </script>
@endsection
