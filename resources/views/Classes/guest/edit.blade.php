@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item"><a href="{{ route('tenant.classes.guests.index') }}" title="" class="text-grey">Guests</a></span>
            <span class="breadcrumb-item active">{{ $record->full_name }}</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h4 class="card-title">{{ $record->full_name }}</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.classes.guests.show', ['id' => $record->id]) }}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <div class="tab-content">
                        <div class="tab-pane active">

                            <form action="{{ route('tenant.classes.guests.update', ['guest' => $record]) }}" method="POST">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3"><h6>Details</h6></div>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* First Name</label>
                                                        <input type="text" name="fname" placeholder="First Name" class="form-control @error('fname') is-invalid @enderror" maxlength="255" required value="{{ old('fname', $record->fname) }}">
                                                        @error('fname')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Last Name</label>
                                                        <input type="text" name="lname" placeholder="Last Name" class="form-control @error('lname') is-invalid @enderror" maxlength="255" required value="{{ old('lname', $record->lname) }}">
                                                        @error('lname')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Email</label>
                                                        <input type="email" name="email" placeholder="Email Address" class="form-control @error('email') is-invalid @enderror" maxlength="255" required value="{{ old('email', $record->email) }}">
                                                        @error('email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Phone Number</label>
                                                        <input type="text" name="phone" placeholder="Phone Number" class="form-control @error('phone') is-invalid @enderror" maxlength="255" value="{{ old('phone', $record->phone) }}">
                                                        @error('phone')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Company</label>
                                                        <input type="text" name="company" placeholder="Company Name" class="form-control @error('company') is-invalid @enderror" maxlength="255" value="{{ old('company', $record->company) }}">
                                                        @error('company')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3"><h6>Address</h6></div>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Street</label>
                                                        <input type="text" name="street" placeholder="Street Name" class="form-control @error('street') is-invalid @enderror" maxlength="255" value="{{ old('street', $record->street) }}">
                                                        @error('street')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>City</label>
                                                        <input type="text" name="city" placeholder="City Name" class="form-control @error('city') is-invalid @enderror" maxlength="255" value="{{ old('city', $record->city) }}">
                                                        @error('city')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Zip Code</label>
                                                        <input type="text" name="zip" placeholder="Zip Code" class="form-control @error('zip') is-invalid @enderror" maxlength="255" value="{{ old('zip', $record->zip) }}">
                                                        @error('zip')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Country</label>
                                                        <select class="form-control select-no-search" data-fouc name="country">
                                                            <option value="">-- Please Select --</option>
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country->country_name }}" @selected(old('country', $record->country) == $country->country_name)>{{ $country->country_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="text-right">
                                        @csrf
                                        @method('PUT')
                                        <button class="btn bg-danger new-room">Submit</button>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
