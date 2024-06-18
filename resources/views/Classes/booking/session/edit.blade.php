@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
                <a href="{{ route('tenant.classes.bookings.index') }}" class="breadcrumb-item">Classes</a>
                <a href="{{ route('tenant.classes.bookings.show', ['ref' => $booking->ref]) }}" class="breadcrumb-item"># {{ $booking->ref }}</a>
                <span class="breadcrumb-item active">Edit Guest</span>
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
                        <h4 class="card-title">Edit Guest #{{ $record->first_name }}</h4>
                        <div class="header-elements">
                            <a href="{{ route('tenant.classes.bookings.show', ['ref' => $booking->ref]) }}" title="" class="btn btn-link text-danger">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>
                    <div class="card-body border-0 p-0">
                        <form action="{{ route('tenant.classes.bookings.sessions.update', ['ref' => $booking->ref, 'id' => $record->id]) }}" method="POST">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-4"><h6>Guest</h6></div>
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>First name</label>
                                                    <input type="text" name="first_name" placeholder="Name" class="form-control @error('first_name') is-invalid @enderror" maxlength="255" value="{{ old('first_name', $record->first_name) }}">
                                                    @error('first_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Surname</label>
                                                    <input type="text" name="last_name" placeholder="Surname" class="form-control @error('last_name') is-invalid @enderror" maxlength="255" value="{{ old('last_name', $record->last_name) }}">
                                                    @error('last_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="text" name="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror" maxlength="255" value="{{ old('email', $record->email) }}">
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Weight</label>
                                                    <input type="number" name="weight" placeholder="Weight" min="30" max="200" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight', $record->weight) }}">
                                                    @error('weight')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                    <button class="btn bg-danger" name="submit" type="submit">Submit</button>
                                </div>
                            </div>
                            <!-- end card body -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
