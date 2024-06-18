@extends('Booking.app')

@section('content')
<!-- Page content -->
<div class="page-content min-vh-100">
    <!-- Main content -->
    <div class="content-wrapper">
        <!-- Content area -->
        <div class="content d-flex justify-content-center align-items-center">
            <!-- Login form -->
            <form class="login-form" action="{{ route('tenant.login.attempt') }}" method="post">
                <div class="card mb-0 border-0">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="/images/new-logo.png" alt="{{ config('app.name') }}" style="max-width: 220px;"/>
                            <br /><br />
                            <h5 class="mb-0">Login to your account</h5>
                            <span class="d-block text-muted">Enter your credentials below</span>
                        </div>

                        @include('Booking.partials.form-errors')
                        @include('Booking.partials.form-messages')

                        <div class="form-group form-group-feedback form-group-feedback-left mb-2">
                            <input type="text" class="form-control" name="email" placeholder="Email" value="{{ old('email' )}}" />
                            <div class="form-control-feedback"><i class="icon-envelope text-muted"></i></div>
                        </div>

                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input type="password" class="form-control" name="password" placeholder="Password" value="{{ old('password' )}}" />
                            <div class="form-control-feedback"><i class="icon-lock2 text-muted"></i></div>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn bg-kima btn-block">
                                LOG IN <i class="fal fa-long-arrow-right ml-1"></i>
                            </button>
                            @csrf
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
