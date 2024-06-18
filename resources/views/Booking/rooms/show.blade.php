@extends('Booking.app')

@section('content')

@include('Booking.partials.rooms.add-pricing-calendar')

<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
  <div class="d-flex">
    <div class="breadcrumb">
      <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <span class="breadcrumb-item">Settings</span>
      <span class="breadcrumb-item"><a href="{{ route('tenant.rooms') }}" title="" class="text-grey">Room Categories</a></span>
      <span class="breadcrumb-item active">{{ $room->name }}</span>
    </div>

    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
  </div>
</div>

<div class="page-content">
  <div class="content-wrapper container">
    <div class="content">
      <div class="card">
        <div class="card-header header-elements-inline">
          <h4 class="card-title">{{ $room->name }}</h4>
          <div class="header-elements">
            <a href="{{ route('tenant.rooms') }}" title="" class="btn btn-link text-slate">
              <i class="far fa-angle-left mr-1"></i> Return
            </a>
          </div>
        </div>
        <div class="card-body p-0 border-0">
          <ul class="nav nav-tabs nav-tabs-highlight justify-content-center" id="room-tab">
            <li class="nav-item">
              <a href="#room-details" class="nav-link" data-toggle="tab">
                <div>
                  <i class="icon-file-text d-block mb-1 mt-1"></i>
                  Room Details
                </div>
              </a>
            </li>
            <li class="nav-item">
              <a href="#pricing" class="nav-link" data-toggle="tab">
                <div>
                  <i class="icon-cash d-block mb-1 mt-1"></i>
                  Pricing
                </div>
              </a>
            </li>
            <li class="nav-item">
              <a href="#pricing-calendar" class="nav-link" data-toggle="tab">
                <div>
                  <i class="icon-calendar d-block mb-1 mt-1"></i>
                  Pricing Calendar
                </div>
              </a>
            </li>
            <li class="nav-item">
              <a href="#rooms" class="nav-link" data-toggle="tab">
                <div>
                  <i class="icon-bed2 d-block mb-1 mt-1"></i>
                  Rooms
                </div>
              </a>
            </li>
            <li class="nav-item">
              <a href="#images" class="nav-link" data-toggle="tab">
                <div>
                  <i class="icon-images2 d-block mb-1 mt-1"></i>
                  Images
                </div>
              </a>
            </li>
          </ul>
        </div>
        <div class="card-body border-0 p-0">
          <div class="tab-content">
            <div class="tab-pane fade" id="room-details">
              @include('Booking.partials.rooms.room-details')
            </div>

            <div class="tab-pane fade" id="pricing">
              @include('Booking.partials.rooms.room-prices')
            </div>

            <div class="tab-pane fade" id="pricing-calendar">
              @include('Booking.partials.rooms.pricing-calendar')
            </div>

            <div class="tab-pane fade" id="rooms">
              @include('Booking.partials.rooms.room-rooms')
            </div>

            <div class="tab-pane fade" id="images">
              @include('Booking.partials.rooms.images')
            </div>

          </div>
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
  $('#room-tab a[href="'+ hash +'"]').tab('show');
}
$('a.nav-link').on('click', function (e) {
  window.location.hash = $(this).attr('href');
});
$('.daterange-empty').daterangepicker({
  autoApply: true,
  showDropdowns: true,
  minDate: "01/01/2018",
  minYear: 2018,
  maxYear: 2030,
  autoUpdateInput: true,
  locale: {
    format: 'DD.MM.YYYY'
  }
});
/*
$('.daterange-empty').on('apply.daterangepicker', function(ev, picker) {
  $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
});

$('.daterange-empty').on('cancel.daterangepicker', function(ev, picker) {
  $(this).val('');
});*/
</script>
@endsection
