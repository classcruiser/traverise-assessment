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
                    <h4 class="card-title">New Class Booking</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.classes.bookings.index') }}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <form action="{{ route('tenant.classes.bookings.store', ['cart' => json_encode($cart)]) }}" method="POST">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <h6>Booker Details</h6>
                                </div>
                                <div class="col-sm-8">
                                    <ul class="nav nav-tabs mb-3" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a href="#existing-guest" class="nav-link active" data-toggle="tab" aria-selected="true" role="tab">
                                                Existing guest
                                            </a>
                                        </li>

                                        <li class="nav-item" role="presentation">
                                            <a href="#new-guest" class="nav-link" data-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                                New guest
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="existing-guest" role="tabpanel">
                                            <select class="form-control select-remote-data" name="guest_email" data-fouc data-placeholder="Search guest...">
                                                @if (old('guest_email'))
                                                    <option value="{{ old('guest_email') }}" selected>{{ old('guest_email') }}</option>
                                                @else
                                                    <option></option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="tab-pane fade" id="new-guest" role="tabpanel">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* First name</label>
                                                        <input type="text" name="first_name" placeholder="Name" class="form-control @error('first_name') is-invalid @enderror" maxlength="255" value="{{ old('first_name') }}">
                                                        @error('first_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Surname</label>
                                                        <input type="text" name="last_name" placeholder="Surname" class="form-control @error('last_name') is-invalid @enderror" maxlength="255" value="{{ old('last_name') }}">
                                                        @error('last_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Email</label>
                                                        <input type="text" name="email" placeholder="email" class="form-control @error('email') is-invalid @enderror" maxlength="255" value="{{ old('email') }}">
                                                        @error('email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Phone</label>
                                                        <input type="text" name="phone" placeholder="Phone number (number only)" class="form-control @error('phone') is-invalid @enderror" maxlength="255" value="{{ old('phone') }}">
                                                        @error('phone')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Address</label>
                                                        <input type="text" name="address" placeholder="Address" class="form-control @error('address') is-invalid @enderror" maxlength="255" value="{{ old('address') }}">
                                                        @error('address')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Country</label>
                                                        <select class="form-control select-no-search" data-fouc name="country">
                                                            <option value="">-- Please Select --</option>
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country->country_name }}" {{ old('country', 'Germany') == $country->country_name ? 'selected' : '' }}>{{ $country->country_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('country')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4"><h6>Class & Guest</h6></div>
                                <div class="col-sm-8">
                                    @php $index = 0; @endphp
                                    @foreach ($cart as $class)
                                        @for ($i = 0; $i < $class['quantity']; $i++)
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="font-weight-bold"><input type="checkbox" name="guest[{{ $index }}][include]" value="on" @checked(old('guest.'. $index .'.include', true)) class="mr-1" /> {{ $index + 1 }}. {{ $class['name'] }}</span>
                                                        <span>{{ $class['date'] }} {{ $class['time'] }}</span>
                                                        <input type="hidden" name="guest[{{ $index }}][class_schedule_id]" value="{{ $class['id'] }}" />
                                                        <input type="hidden" name="guest[{{ $index }}][class_session_id]" value="{{ $class['class_session_id'] }}" />
                                                        <input type="hidden" name="guest[{{ $index }}][class_date]" value="{{ $class['class_date'] }}" />
                                                        <input type="hidden" name="guest[{{ $index }}][class_name]" value="{{ $class['name'] }}" />
                                                    </label>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>First name</label>
                                                        <input type="text" name="guest[{{ $index }}][first_name]" placeholder="Name" class="form-control @error('guest.'. $index .'.first_name') is-invalid @enderror" maxlength="255" value="{{ old('guest.'. $index .'.first_name') }}">
                                                        @error('guest.'. $index .'.first_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>Surname</label>
                                                        <input type="text" name="guest[{{ $index }}][last_name]" placeholder="Surname" class="form-control @error('guest.'. $index .'.last_name') is-invalid @enderror" value="{{ old('guest.'. $index .'.last_name') }}">
                                                        @error('guest.'. $index .'.last_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>Email</label>
                                                        <input type="text" name="guest[{{ $index }}][email]" placeholder="Email" class="form-control @error('guest.'. $index .'.email') is-invalid @enderror" maxlength="255" value="{{ old('guest.'. $index .'.email') }}">
                                                        @error('guest.'. $index .'.email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>Weight</label>
                                                        <input type="number" name="guest[{{ $index }}][weight]" placeholder="Weight" min="30" max="200" class="form-control @error('guest.'. $index .'.weight') is-invalid @enderror" value="{{ old('guest.'. $index .'.weight') }}">
                                                        @error('guest.'. $index .'.weight')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                @if (!$loop->last)
                                                    <div class="col-sm-12">
                                                        <div class="mt-1 mb-3" style="border-top: 1px solid #eee;"></div>
                                                    </div>
                                                @endif
                                            </div>
                                            @php $index++ @endphp
                                        @endfor
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4"><h6>Booking Details</h6></div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>* Status</label>
                                                <select class="form-control select-no-search" data-fouc name="status">
                                                    <option value="DRAFT">DRAFT</option>
                                                    <option value="CONFIRMED">CONFIRMED</option>
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

@section('scripts')
<script>
    var url = document.location.toString();
    if (url.match('#')) {
        var hash = '#' + (url.split('#')[1]);
        $('.nav-tabs a[href="' + hash + '"]').tab('show');
    }
    $('a.nav-link').on('click', function(e) {
        window.location.hash = $(this).attr('href');
    });
</script>
@endsection
