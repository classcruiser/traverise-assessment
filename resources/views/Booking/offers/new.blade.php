@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
  <div class="d-flex">
    <div class="breadcrumb">
      <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <span class="breadcrumb-item">Settings</span>
      <a href="{{ route('tenant.special-offers') }}" title="" class="breadcrumb-item">Special Offers</a>
      <span class="breadcrumb-item active">Add new special offer</span>
    </div>

    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
  </div>
</div>

<div class="page-content">
  <div class="content-wrapper container">
    <div class="content">
      <div class="card">
        <div class="card-header bg-transparent header-elements-inline">
          <h4 class="card-title">Add Special Offer</h4>
          <div class="header-elements">
            <a href="{{ route('tenant.special-offers') }}" title="" class="btn btn-link text-slate">
              <i class="far fa-angle-left mr-1"></i> Return
            </a>
          </div>
        </div>

        <form action="{{ route('tenant.special-offers.insert') }}" method="post" id="new-offer">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-4"><h6>Details</h6></div>
              <div class="col-sm-8">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>* Name</label>
                      <input type="text" name="name" placeholder="Name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>* Discount type</label>
                      <select class="form-control select-no-search" data-fouc data-placeholder="Type" name="discount_type" required>
                        <option></option>
                        <option value="Percent">Percent</option>
                        <option value="Fixed">Fixed</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>* Discount value</label>
                      <input type="text" name="discount_value" placeholder="Value" class="form-control" value="{{ old('discount_value') }}" required>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-4"><h6>Conditions</h6></div>
              <div class="col-sm-8">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Stay condition</label>
                      <select class="form-control select-no-search" data-fouc data-placeholder="Stay condition" name="stay_type">
                        <option></option>
                        <option value="Check-In date in">Check-In date in</option>
                        <option value="Check-Out date in">Check-Out date in</option>
                        <option value="Whole stay in">Whole stay in</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Stay dates</label>
                      <input type="text" name="stay_value" placeholder="Dates" class="form-control daterange-basic" value="{{ old('stay_value') }}" autocomplete="off">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Booked between</label>
                      <input type="text" name="booked_between" placeholder="Dates" class="form-control daterange-basic" value="{{ old('booked_between') }}" autocomplete="off">
                    </div>
                  </div>
                  <div class="col-sm-6"></div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Number of guests</label>
                      <div class="d-flex justify-content-between align-items-center">
                        <input type="number" name="min_guest" placeholder="Min guest" class="form-control mr-2" value="{{ old('min_guest') }}">
                        &mdash;
                        <input type="number" name="max_guest" placeholder="Max guest" class="ml-2 form-control" value="{{ old('max_guest') }}">
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Stay length</label>
                      <div class="d-flex justify-content-between align-items-center">
                        <input type="number" name="min_stay" placeholder="Min stay length" class="form-control mr-2" value="{{ old('min_stay') }}">
                        &mdash;
                        <input type="number" name="max_stay" placeholder="Max stay length" class="ml-2 form-control" value="{{ old('max_stay') }}">
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            
          </div>

          <div class="card-body">
            <div class="row">
              <div class="col-sm-4"><h6>Room associations</h6></div>
              <div class="col-sm-8">
                <div class="row">

                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Room category</label>

                      <div class="border-1 border-alpha-grey">
                        @foreach ($locations as $location)
                          <div class="py-2 px-3 alpha-grey {{ $loop->last ? '' : 'border-bottom-1 border-alpha-grey' }}"><i class="fa fa-fw fa-home mr-1"></i> <b>{{ $location->name }}</b></div>
                          @foreach ($location->rooms as $room)
                            <div class=" py-2 px-3 border-bottom-1 border-alpha-grey">
                              <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="room-{{ $room->id }}" name="rooms[{{ $room->id }}]">
                                <label class="custom-control-label" for="room-{{ $room->id }}">{{ $room->name }}</label>
                              </div>
                            </div>
                          @endforeach
                        @endforeach
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            
          </div>

          @can ('save setting')
            <div class="card-body">
              <div class="text-right">
                {!! csrf_field() !!}
                <button class="btn bg-danger">Submit</button>
              </div>
            </div>
          @endcan
          <!-- end card body -->

        </form>

      </div>
    </div>

  </div>
</div>

@endsection

@section('scripts')
<script>
tippy('.tippy', {
  content: 'Tooltip',
  arrow: true,
})
$('.daterange-basic').daterangepicker({
  autoApply: false,
  autoUpdateInput: false,
  locale: {
    format: 'DD.MM.YYYY',
    cancelLabel: "Clear"
  }
});
$('.daterange-basic').on('apply.daterangepicker', function(ev, picker) {
  $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
});
</script>
@endsection