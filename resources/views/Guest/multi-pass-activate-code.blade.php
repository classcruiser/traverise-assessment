@extends('Guest.app')

@section('content')
    <div class="w-screen min-h-screen bg-gray-50 relative">
        <x-auth.container>
            <x-auth.header/>
            <div class="py-8 px-6">
                @if (session()->has('message'))
                    <div class="mb-6 p-4 bg-sky-50 border border-sky-100 text-sky-600 text-sm leading-relaxed">
                        {{ session('message') }}
                    </div>
                @endif
                <guest-multi-pass-activate-code :guest-init="{{ json_encode($guest) }}" />
            </div>
        </x-auth.container>
    </div>
@endsection
