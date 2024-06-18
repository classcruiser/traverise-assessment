<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Traverise Booking System</title>
    <link rel="stylesheet" href="/css/session-app.css?{{ date('Ymd') }}" media="screen" />
    <link rel="stylesheet" href="/css/fontawesome-all.min.css?{{ date('Ymd') }}" media="screen" />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.4/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'resourceTimelineWeek',
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives'
            });
            calendar.render();
        });
    </script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
    @yield('content')
    @yield('json')
        <script src="{{ asset('js/session-app.js?'. date('Ymdhi')) }}"></script>
    @yield('scripts')
    @yield('subscripts')
</body>
</html>
