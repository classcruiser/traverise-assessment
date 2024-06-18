@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
  <div class="d-flex">
    <div class="breadcrumb">
      <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
      <a href="{{ route('tenant.bookings') }}" class="breadcrumb-item">Bookings</a>
      <a href="{{ route('tenant.bookings.show', [ 'ref' => $booking->ref ]) }}" class="breadcrumb-item">#{{ $booking->ref }}</a>
      <span class="breadcrumb-item active">Guest Rooms</span>
    </div>

    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
  </div>
</div>

<div class="page-content pt-4">

  @include('Booking.bookings.sidebar')

  <div class="content-wrapper container">
    <div class="content">
      <div class="card">
        <div class="card-header bg-transparent header-elements-inline">
          <h6 class="card-title"><i class="fa fa-bed mr-1"></i> <b>Rooms for {{ $guest->full_name }}</b></h6>
        </div>
        <form action="#" method="post">
          <div class="card-body border-0">
            <div class="row">
              <div class="col-sm-4"><h6>Guest Details</h6></div>
              <div class="col-sm-8">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>* Fist Name</label>
                      <input type="text" name="fname" placeholder="First name" class="form-control" value="">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>* Last Name</label>
                      <input type="text" name="lname" placeholder="Last name" class="form-control" value="">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="card-body">
            <div class="d-flex justify-content-end align-items-center">
              <button type="submit" class="btn bg-slate btn-lg">Submit</button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>

</div>
@endsection
