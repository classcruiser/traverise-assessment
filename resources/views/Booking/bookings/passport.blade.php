@extends('main')

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
                  <h2 class="mb-0">Passport Details</h2>
                  <h6 class="text-grey font-weight-regular">Booking #{{ $booking->ref }}</h6>
                </div>
                <div>
                  <h5 class="mb-0">{{ $booking->guest->details->full_name }}</h5>
                  <p>{{ $booking->guest->details->email }}</p>
                </div>
              </div>

              <hr class="border-alpha-grey" />

              @if ($finish)
                <p>Thank you for adding your passport details. If you want to change it, please contact us at <b><a href="mailto:info@kimasurf.com" title="" class="text-danger">info@kimasurf.com</a></b>
              @else
                @include('Booking.partials.form-errors')

                <p>Please upload the passport for each of the guests. Picture must be in JPG format.</p>

                <p class="mb-3">
                  <b>EXAMPLE</b>
                  <br /><br />
                  <img src="/images/passport-example.jpg" alt="" class="img-fluid" />
                </p>

                <hr class="border-alpha-grey" />

                <form action="/passport-details/{{ $key }}" class="form-horizontal" enctype="multipart/form-data" method="post">
                  @foreach ($booking->guests as $guest)
                    <div class="form-group row">
                      <label class="col-sm-4">
                        <b>{{ strtoupper($guest->details->full_name) }}</b>
                        <br />
                        {{ $guest->details->email }}
                      </label>
                      <div class="col-sm-8">
                        @if (!$guest->passport)
                          <input type="file" name="passport[{{ $guest->booking->id }}_{{ $guest->id }}]" class="form-control form-control-sm d-block" />
                        @else
                          <p class="font-weight-bold text-primary">DONE</p>
                        @endif
                      </div>
                    </div>
                  @endforeach
                  {!! csrf_field() !!}
                  <hr class="border-alpha-grey" />
                  <div class="d-flex flex-column align-items-center justify-content-center">
                    <label><input type="checkbox" name="confirmed" class="mr-1 mb-2" required /> Yes, I have upload the correct passport details</label>
                    <button type="submit" class="btn bg-danger transfer-submit-button">Submit Passport Details</button>
                  </div>
                </form>
              @endif

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection