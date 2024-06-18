@extends('Booking.main', ['bootstrap' => false])
@section('customcss')
<style type="text/css" media="screen">
    body {
        background-color: {{ $tenant_settings->bg_color }};
    }
    .bg-custom-primary {
        background-color: {{ $tenant_settings->primary_color }};
    }
    .bg-custom-secondary {
        background-color: {{ $tenant_settings->secondary_color }};
    }
    .navi li span { color: #444 }
    .navi li.navi-active span {
        border-color: {{ $tenant_settings->accent_color }};
        color: {{ $tenant_settings->accent_color }};
    }
    .navi li.navi-complete:after {
        background-color: {{ $tenant_settings->accent_color }};
    }
    .btn-custom {
        background-color: {{ $tenant_settings->accent_color }};
    }
    .text-custom, .link-custom, .normal-text a {
        color: {{ $tenant_settings->accent_color }};
    }
    .text-custom:hover, .link-custom:hover, .normal-text a:hover {
        color: {{ $tenant_settings->accent_color }};
    }
    .border-danger {
        border-color: {{ $tenant_settings->accent_color }};
    }
    .normal-text {

    }
</style>
@endsection

@section('content')
<div class="w-screen h-screen flex justify-center items-center flex-col">
    
    <div class="w-full md:w-2/4 mx-4 md:mx-0 rounded-sm">
        <div class="text-xl text-gray-800 p-5 bg-white flex justify-between items-start">
        	<img src="{{ asset('images/camps/'. tenant('id') .'_logo.jpg?'. date('Ymd')) }}" alt="{{ $tenant_settings->title }}" class="block w-64 mb-2" />
        	<span class="text-sm">Last update: {{ $document->updated_at->format('d.m.Y H:i') }}</span>
        </div>
        <div id="popup-body" class="p-5 border-t border-gray-100 leading-relaxed bg-white text-gray-800 max-h-[85vh] md:max-h-[90vh] overflow-y-auto normal-text text-sm">
        	<h1 class="text-custom text-xl font-bold mb-3">{{ $document->title }}</h1>
        	{!! $document->content !!}
        </div>
    </div>
</div>
@endsection