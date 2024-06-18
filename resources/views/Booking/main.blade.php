<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $tenant_settings->title }} Booking page</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico?{{ date('Ymd') }}">
    <link rel="stylesheet" href="/css/booking-extend.css?{{ date('Ymd') }}" media="screen" />
    <link rel="stylesheet" href="{{ asset('css/fontawesome-all.min.css') }}" media="screen" />
    <link rel="stylesheet" href="{{ asset('css/icomoon/styles.css') }}" media="screen" />
    @if ((isset($bootstrap) && $bootstrap) || !isset($bootstrap))
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" media="screen" />
        <link rel="stylesheet" href="{{ asset('css/bootstrap_limitless.min.css') }}" media="screen" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <link rel="stylesheet" href="{{ asset('css/layout.min.css') }}" media="screen" />
        <link rel="stylesheet" href="{{ asset('css/components.min.css') }}" media="screen" />
        <link rel="stylesheet" href="{{ asset('css/colors.min.css') }}" media="screen" />
        <link rel="stylesheet" href="{{ asset('css/booking.css?'. date('Ymd')) }}" media="screen" />
        <link rel="stylesheet" href="/css/hotel-datepicker.css?{{ date('Ymd') }}" media="screen" />
        <link rel="stylesheet" href="{{ asset('css/intelinput/intlTelInput.min.css') }}" media="screen" />
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @yield('customcss')
    @if ($tenant_settings->jscript_header)
        {!! $tenant_settings->jscript_header !!}
    @endif
</head>
<body>
    @routes
    @yield('gtagscripts')
    @yield('content')
    <script src="{{ asset('js/plugins.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/intelinput/intlTelInput.min.js') }}"></script>
    <script src="/js/fecha.min.js?{{ date('Ymd') }}"></script>
    <script src="/js/hotel-datepicker.min.js?{{ date('Ymd') }}"></script>
    @yield('scripts')
    @yield('subscripts')
    @if ($tenant_settings->jscript_body)
        {!! $tenant_settings->jscript_body !!}
    @endif
</body>
</html>
