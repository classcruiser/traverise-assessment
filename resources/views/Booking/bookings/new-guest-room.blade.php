@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
  <div class="d-flex">
    <div class="breadcrumb">
      <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
      <a href="{{ route('tenant.bookings') }}" class="breadcrumb-item">Bookings</a>
      <a href="{{ route('tenant.bookings.show', [ 'ref' => $booking->ref ]) }}" class="breadcrumb-item">#{{ $booking->ref }}</a>
      <span class="breadcrumb-item active">New Room</span>
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
          <h6 class="card-title"><i class="fa fa-bed mr-1"></i> <b>New room for {{ $guest->full_name }}</b></h6>
        </div>
        <div class="card-body border-0">
          <div class="row">
            <div class="col-sm-4"><h6>Room Search</h6></div>
            <div class="col-sm-8">
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>* Stay</label>
                    <div class="input-group">
                      <span class="input-group-prepend">
                        <span class="input-group-text"><i class="icon-calendar22"></i></span>
                      </span>
                      <input type="text" class="form-control daterange-basic daterange-basic-search" id="room-search-dates" value="{{ $check_in .' - '. $check_out }}">
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>* Location</label>
                    <select class="form-control select" data-fouc data-placeholder="Location" name="location" {{ $booking->rooms->count() > 0 ? 'disabled' : '' }}>
                      <option></option>
                      @foreach ($locations as $location)
                        <option value="{{ $location->id }}" {{ $booking->location_id == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end align-items-center">
            <input type="hidden" id="guest_id" value="{{ $booking_guest_id }}" />
            <input type="hidden" id="ref" value="{{ $booking->ref }}" />
            <input type="hidden" id="booking_id" value="{{ $booking->id }}" />
            <input type="hidden" id="booking_status" value="{{ $booking->status }}" />
            <input type="hidden" id="action" value="new" />
            <a href="{{ route('tenant.bookings.show', ['ref' => $booking->ref]) }}" title="" class="text-muted mr-3">Return</a>
            <button type="submit" class="btn bg-danger btn-room-search">Search Availability</button>
          </div>
        </div>

      </div>

      <div class="bg-transparent p-0 mt-2 search-container">
        <div id="search-result">
          <div class="text-center alpha-slate px-2 py-5">Search something first</div>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
window.IS_AGENT = {{ Auth::user()->role_id == 4 ? 1 : 0 }};
$('.daterange-basic').daterangepicker({
  autoApply: true,
  locale: {
    format: 'DD.MM.YYYY'
  }
});
</script>
@endsection
