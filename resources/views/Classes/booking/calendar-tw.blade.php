@extends('Booking.app', ['bootstrap' => false, 'tailwind' => true, 'header' => false, 'footer' => false])

@section('content')
<div id="calendar-events"></div>
<link rel="stylesheet" href="/css/class.css?{{ date('Ymd') }}" media="screen" />
@endsection

@section('scripts')
<script>
    window.dates = @json($dates);
    window.date_labels = @json($date_labels);
    window.schedules = @json($schedules);
    window.categories = @json($categories);
    window.category = @json($category);
    window.date = '{{ request()->has('date') ? request('date') : date('Y-m-d') }}';
    window.is_admin = true;
    window.next = '{{ request()->has('next') ? request('next') : '' }}';
    window.footer = {{ request()->has('footer') ? request('footer') : 'true' }};
</script>
<script src="/js/shop-calendar.js"></script>
@endsection