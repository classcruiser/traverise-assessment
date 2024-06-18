@extends('Guest.app')

@section('content')
    <div class="w-screen min-h-screen bg-gray-50 relative">
        <x-shop.container>
            <x-shop.header/>
            <div class="py-8 px-6">
                @if (session()->has('message'))
                    <div class="mb-6 p-4 bg-sky-50 border border-sky-100 text-sky-600 text-sm leading-relaxed">
                        {{ session('message') }}
                    </div>
                @endif
                <guest-reset-pass
                    token="{{ $token }}"
                    email="{{ $email }}"
                />
            </div>
        </x-shop.container>
    </div>
@endsection
