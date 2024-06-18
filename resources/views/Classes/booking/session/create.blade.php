@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
                <a href="{{ route('tenant.classes.bookings.index') }}" class="breadcrumb-item">Classes</a>
                <a href="{{ route('tenant.classes.bookings.show', ['ref' => $booking->ref]) }}" class="breadcrumb-item"># {{ $booking->ref }}</a>
                <span class="breadcrumb-item active">New Session</span>
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
                        <h4 class="card-title">New Class Session</h4>
                        <div class="header-elements">
                            <a href="{{ route('tenant.classes.bookings.sessions.index', ['ref' => $booking->ref]) }}" title="" class="btn btn-link text-danger">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>
                    <div class="card-body border-0 p-0">
                        <form action="{{ route('tenant.classes.bookings.sessions.store', ['ref' => $booking->ref, 'cart' => json_encode($cart)]) }}" method="POST">
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
