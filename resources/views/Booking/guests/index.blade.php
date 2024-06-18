@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
  <div class="d-flex">
    <div class="breadcrumb">
      <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <span class="breadcrumb-item active">Guests</span>
    </div>

    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
  </div>
</div>

<div class="page-content">
  <div class="content-wrapper">
    <div class="content">
      @include('Booking.partials.form-messages')
      <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
        <h4 class="m-0 mr-auto d-none d-md-block"><i class="fal fa-fw fa-calendar-alt mr-1"></i> All Guests</h4>
      <!--  @can ('add setting')
          <a href="{{ route('tenant.guests') }}" title="" class="btn bg-danger">
            <i class="far fa-plus mr-1"></i> New Guest
          </a>
        @endcan -->
      </div>

      <div class="card">
        <div class="table-responsive">
          <table class="table table-xs table-compact dark">
            <thead>
              <tr class="bg-grey-700">
                <th class="two wide">Name</th>
                <th>Client ID</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Country</th>
                <th class="text-center">Marketing</th>
                <th class="text-center">Bookings</th>
                <th class="text-center">Repeater</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($guests as $guest)
                @php
                    $stay = $bs->getTotalStayByBookings($guest->bookings);
                @endphp
                <tr>
                  <td>
                    <a href="{{ route('tenant.guests.show', [ 'id' => $guest->id ]) }}" class="text-danger"><b>{{ $guest->full_name }}</b></a>
                  </td>
                  <td class="text-info">{{ $guest->client_number ?? '--' }}</td>
                  <td>{{ $guest->email }}</td>
                  <td>{{ $guest->phone ?? '--' }}</td>
                  <td>{{ strtoupper($guest->country) }}</td>
                  <td class="text-center">
                    <i class="far {{ $guest->marketing_flag ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                  </td>
                  <td class="text-center">{{ $guest->bookings_count }}</td>
                  <td class="text-center">{!! $stay['html'] !!}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="d-md-flex justify-content-between align-items-center">
        <div>{{ $guests->appends($_GET)->links() }}</div>
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
});
$('.date-basic').daterangepicker({
  autoApply: false,
  autoUpdateInput: false,
  singleDatePicker: true,
  locale: {
    format: 'DD.MM.YYYY',
    cancelLabel: "Clear"
  }
});
$('.date-basic').on('apply.daterangepicker', function(ev, picker) {
  $(this).val(picker.startDate.format('DD.MM.YYYY'));
});
$('.daterange-empty').daterangepicker({
  autoApply: true,
  showDropdowns: true,
  minDate: "01/01/2018",
  minYear: 2018,
  maxYear: 2030,
  autoUpdateInput: false,
  locale: {
    format: 'DD.MM.YYYY'
  },
  ranges: {
    'Today': [moment(), moment()],
    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
    'This Week': [moment().startOf('week'), moment().endOf('week')],
    'Last Week': [moment().startOf('week').subtract(7, 'days'), moment().endOf('week').subtract(7, 'days')],
    'This Month': [moment().startOf('month'), moment().endOf('month')],
    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
    'This Year': [moment().startOf('year'), moment().endOf('year')],
    'Last Year': [moment().startOf('year').subtract(1, 'year'), moment().endOf('year').subtract(1, 'year')],
  },
  alwaysShowCalendars: true,
});
$('.daterange-empty').on('apply.daterangepicker', function(ev, picker) {
  $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
});

$('.daterange-empty').on('cancel.daterangepicker', function(ev, picker) {
  $(this).val('');
});
</script>
@endsection
