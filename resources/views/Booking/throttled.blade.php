@extends('Booking.app')

@section('content')
<div class="page-content">
  <!-- Main content -->
  <div class="content-wrapper">
    <!-- Content area -->
    <div class="content d-flex justify-content-center align-items-center">
    <!-- Login form -->
    <form class="login-form" action="/auth/login" method="post">
      <div class="card mb-0 font-size-lg">
        <div class="card-body">
          <div class="text-center mb-0">
            <i class="far fa-ban fa-5x text-danger"></i>
            <br /><br />
            <h5 class="mb-2 text-danger"><b>BLOCKED</b></h5>
            <p class="mb-0">Too many failed attempt.</p>
            <p class="mb-0">You can try again after <b>{{ $retry }} minutes</b> or you can contact admin to resolve the issue.</p>
          </div>

        </div>
      </div>
    </form>
    <!-- /login form -->
    </div>
    <!-- /content area -->
  </div>
  <!-- /main content -->
  </div>
  <!-- /page content -->
</div>
@endsection
