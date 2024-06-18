@extends('Classes.app', ['scripts' => [
    'js/shop-calendar.js?v=1.0.0',
]])

@section('content')
<div class="w-screen min-h-screen bg-gray-50 relative">
    <x-shop.container>
        <x-shop.header />
        <x-shop.heading step="1">
            @if (count($schedules) > 0)
                @error('guest')
                    <div class="px-5 py-4 bg-red-50 text-red-600 font-bold text-center border-b border-red-100">{{ $message }}</div>
                @enderror
                <div id="calendar-events"></div>
            @else
                <div class="p-5">Please check back soon</div>
            @endif
        </x-shop.heading>
    </x-shop.container>
</div>
@endsection

@section('scripts')
<script>
    window.dates = @json($dates);
    window.date_labels = @json($date_labels);
    window.schedules = @json($schedules);
    window.categories = @json($categories);
    window.category = @json($category);
    window.date = '{{ request()->has('date') ? request('date') : date('Y-m-d') }}';
</script>
@endsection