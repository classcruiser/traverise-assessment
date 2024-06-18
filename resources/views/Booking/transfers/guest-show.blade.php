@extends('Booking.main')

@section('content')
<div class="page-content pt-4">
  <div class="content-wrapper container">
    <div class="content">
      <div class="row justify-content-center">
        <div class="col-sm-9">
          <img src="/images/logo-email.png" alt="KIMA SURF" style="width: 400px;" class="d-block mx-auto mb-2" />
          <div class="card">
            <div class="card-body p-4">

              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h2 class="mb-0">Transfers Details</h2>
                  <h6 class="text-grey font-weight-regular">Booking #{{ $booking->ref }}</h6>
                </div>
                <div>
                  <h5 class="mb-0">{{ $booking->guest->details->full_name }}</h5>
                  <p>{{ $booking->guest->details->email }}</p>
                </div>
              </div>

              <hr class="border-alpha-grey" />

              @if ($finish)
                <p>Thank you for updating your transfer details. If you want to change it, please contact us at <b><a href="mailto:info@kimasurf.com" title="" class="text-danger">info@kimasurf.com</a></b>
              @else
                @include('partials.form-errors')

                @if ($booking->transfers->count() <= 0)
                  <p>You have already submitted your transfer details. If you want to change it, please contact us at <b><a href="mailto:info@kimasurf.com" title="" class="text-danger">info@kimasurf.com</a></b>
                @else
                  <form action="/transfers-details/{{ $slug }}" class="form-horizontal" enctype="multipart/form-data" method="post">
                    @foreach ($booking->transfers as $transfer)
                      <div class="form-group row">
                        <label class="col-sm-4">
                          {{ strtoupper($transfer->details->name) }}
                          <br />
                          <span class="text-grey-300">{{ $transfer->guests .' '. str_plural('guest', $transfer->guests )}}</span>
                        </label>
                        <div class="col-sm-4">
                          <label class="text-uppercase">Flight Number</label>
                          <input type="text" name="flight_number[{{ $transfer->id }}]" class="form-control form-control-sm" placeholder="Flight number" required />
                        </div>
                        <div class="col-sm-4">
                          <label class="text-uppercase">Flight Time</label>
                          <input type="text" class="form-control date-time-picker" name="flight_time[{{ $transfer->id }}]" autocomplete="off" placeholder="Flight time" required />
                        </div>
                      </div>
                    @endforeach
                    {!! csrf_field() !!}
                    <hr class="border-alpha-grey" />
                    <div class="d-flex flex-column align-items-center justify-content-center">
                      <label><input type="checkbox" name="confirmed" class="mr-1 mb-2" required /> Yes, I have entered the correct transfer details</label>
                      <button type="submit" class="btn bg-danger transfer-submit-button">Submit Transfer Details</button>
                    </div>
                  </form>
                @endif
              @endif

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
$('.date-time-picker').daterangepicker({
  autoUpdateInput: false,
  singleDatePicker: true,
  timePicker: true,
  timePicker24Hour: true,
  autoApply: true,
  locale: {
    cancelLabel: 'Clear',
    format: 'DD.MM.YYYY HH:mm'
  }
});

$('.date-time-picker').on('apply.daterangepicker', function(ev, picker) {
  $(this).val(picker.startDate.format('DD.MM.YYYY HH:mm'));
});
</script>
@endsection