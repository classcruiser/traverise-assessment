@extends('Guest.app')

@section('content')
    <div class="w-screen min-h-screen bg-gray-50 relative">
        <x-shop.container>
            <x-shop.header/>
            <div class="">
                @if (session()->has('message'))
                    <div class="mb-6 p-4 bg-sky-50 border border-sky-100 text-sky-600 text-sm leading-relaxed">
                        {{ session('message') }}
                    </div>
                @endif
                <guest-account :guest="{{ json_encode($guest) }}"
                               :classes-init="{{ json_encode($classes) }}"
                               :multi-pass-orders-init="{{ json_encode($multiPassOrders) }}"
                               :bookings-init="{{ json_encode($bookings) }}"
                />
            </div>
        </x-shop.container>
    </div>
@endsection

