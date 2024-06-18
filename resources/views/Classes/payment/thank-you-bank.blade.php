@extends('Classes.app')

@section('content')
    <div class="bg-neutral-100 min-h-screen">
        <div class="mx-auto px-3 py-10 md:w-1/2">
            <div class="border border-neutral-300 rounded bg-white p-8">
                <h2 class="text-2xl mb-3">Thank you for your payment.</h2>
                <p class="text-sm text-gray-600 leading-snug">
                    You will receive a payment confirmation email within 24 hours after settlement.
                    Feel free to contact our Customer Service Team <a href="mailto:{{tenant('email')}}" title="" class="text-blue-500 hover:text-blue-600">here</a> if you have any questions.
                </p>
            </div>
        </div>
    </div>
@endsection
