<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Traverise Booking System</title>
    <link rel="stylesheet" href="/css/class.css?{{ date('Ymdhi') }}" media="screen" />
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

    <script src="{{ asset('js/plugins.min.js') }}" defer></script>
    @yield('scripts')
    @php
        $scripts = $scripts ?? [];
    @endphp
    @forelse ($scripts as $script)
        <script src="{{ $script }}"></script>
    @empty
        <script src="/js/shop.js?{{ date('Ymdhi') }}"></script>
    @endforelse
</body>
</html>