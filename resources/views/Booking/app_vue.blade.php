<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Traverise Booking System</title>
    @if (isset($tailwind) && $tailwind)
        <link rel="stylesheet" href="/css/booking-extend.css?{{ date('Ymd') }}" media="screen"/>
        <link rel="stylesheet" href="{{ asset('css/booking.css?'. date('Ymd')) }}" media="screen"/>
    @endif
    <link rel="stylesheet" href="{{ asset('css/fontawesome-all.min.css') }}" media="screen"/>
    <link rel="stylesheet" href="{{ asset('css/icomoon/styles.css') }}" media="screen"/>
    @if ((isset($bootstrap) && $bootstrap) || !isset($bootstrap))
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" media="screen"/>
        <link rel="stylesheet" href="{{ asset('css/bootstrap_limitless.min.css') }}" media="screen"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
        <link rel="stylesheet" href="{{ asset('css/layout.min.css') }}" media="screen"/>
        <link rel="stylesheet" href="{{ asset('css/components.min.css') }}" media="screen"/>
        <link rel="stylesheet" href="{{ asset('css/colors.min.css') }}" media="screen"/>
        <link rel="stylesheet" href="/css/class.css?{{ date('Ymd') }}" media="screen" />
        <link rel="stylesheet" href="{{ asset('css/app.css?'. date('Ymd')) }}" media="screen"/>
    @endif
    <link href="{{ asset('/css/dragula.min.css') }}" rel="stylesheet" type="text/css">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
</head>
<body class="{{ Auth::check() ? '' : 'login-body' }}">
@routes

@yield('popups')
@yield('alert')

@if (Auth::check() && ((isset($header) && $header) || !isset($header)))
    @include('Booking.partials.header')
@endif

@yield('login')

<div id="app">
    @yield('content')
</div>

@yield('json')
<script src="{{ mix('js/app.js') }}"></script>
<script src="{{ asset('js/plugins.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.12/lib/draggable.bundle.min.js"></script>
@yield('scripts')
@yield('subscripts')

@if (Auth::check() && ((isset($footer) && $footer) || !isset($footer)))
    @include('Booking.partials.footer')
@endif

</body>
</html>
