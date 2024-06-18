@extends('Classes.app')

@section('content')
    <div class="bg-neutral-100 min-h-screen">
        <div class="mx-auto px-3 py-10 md:w-1/2">
            <div class="border border-neutral-300 rounded bg-white p-8">
                <h2 class="text-2xl mb-3">Oops, failed to complete your payment.</h2>
                <p class="text-sm text-gray-600 leading-snug">
                    You have choose <b>{{ $method }}</b> as your payment method.
                    Please contact <a href="mailto:{{ $profile->contact_email }}" title="" class="text-blue-500 hover:text-blue-600">{{ $profile->contact_email }}</a> if you prefer to use another payment method.
                </p>
            </div>
        </div>
    </div>
@endsection
