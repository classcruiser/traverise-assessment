<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Traverise Booking System</title>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.4/index.global.min.js'></script>
    <link rel="stylesheet" href="/css/class.css?{{ date('Ymd') }}" media="screen" />
    <link rel="stylesheet" href="{{ asset('css/fontawesome-all.min.css') }}" media="screen" />
    <link rel="stylesheet" href="{{ asset('css/colors.min.css') }}" media="screen" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    @yield('css')
</head>
<body>
@routes
    <div id="app">
        @yield('content')
    </div>

    @yield('scripts')
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
</body>
</html>
