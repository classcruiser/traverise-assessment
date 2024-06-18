@extends('Classes.app')

@section('content')
    <div class="bg-neutral-100 min-h-screen">
        <div class="mx-auto px-3 py-10 md:w-1/2">
            <div class="border border-neutral-300 rounded bg-white p-8">
                <h2 class="text-2xl mb-3">Thank you for booking with us.</h2>
                <p class="text-sm text-gray-600 leading-snug mb-4">
                    We have successfully processed your payment. You will receive a payment confirmation email within 24 hours after settlement.
                    Feel free to contact our Customer Service Team <a href="mailto:{{ tenant('email') }}" title="" class="text-blue-500 hover:text-blue-600">here</a> if you have any questions.
                </p>

                <a href="{{ route('tenant.payment.class.invoice', ['id' => $id]) }}" title="" class="text-sm bg-rose-500 hover:bg-rose-600 text-white px-4 py-2 border border-rose-500 hover:border-rose-600 rounded">See your Invoice</a>
            </div>
        </div>
    </div>
@endsection
