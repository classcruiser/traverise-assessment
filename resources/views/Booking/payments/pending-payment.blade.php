@extends('Booking.main')

@section('content')
<div class="page-content pt-4">
  <div class="content-wrapper container">
    <div class="content">
      <div class="row justify-content-center">
        <div class="col-sm-7">
          <div class="card">
            <div class="card-body py-4 px-4">
              <h2>Oops, failed to complete your payment.</h2>
              <div class="payment--body is-inset-24">
                
                <p>
                  You have choose <b>{{ $method }}</b> as your payment method. Please contact <a href="mailto:{{ $profile->contact_email }}" title="">{{ $profile->contact_email }}</a> if you prefer to use another payment method.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
