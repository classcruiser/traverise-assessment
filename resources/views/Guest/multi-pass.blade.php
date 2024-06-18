@extends('Guest.app')

@section('content')
    @include('Classes.shop.popup')

    <div class="w-screen min-h-screen bg-gray-50">
        <x-shop.container>
            <x-shop.header/>
            <div>
                @if (session()->has('message'))
                    <div class="mb-6 p-4 bg-sky-50 border border-sky-100 text-sky-600 text-sm leading-relaxed">
                        {{ session('message') }}
                    </div>
                @endif
                <guest-multi-pass :passes="{{$passes->toJson()}}"
                                  sk="{{ $sk }}"
                                  stripe-account="{{ $stripeAccount }}"
                                  :guest-init="{{ json_encode($guest) }}"
                                  payment-link="{{$paymentLink}}"
                                  :terms="{{ json_encode($terms) }}"
                                  :countries="{{ $countries->toJson() }}"
                                  :payment-methods="{{ json_encode($payment_methods) }}"
                                  :location="{{ json_encode($location) }}"
                ></guest-multi-pass>
            </div>
        </x-shop.container>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.4.slim.min.js"></script>
    <script src="https://js.stripe.com/v3"></script>
    @if ($payments['paypal']['active'])
        <script src="https://www.paypal.com/sdk/js?client-id={{ $payments['paypal']['MODE'] == 'SANDBOX' ? $payments['paypal']['SANDBOX_CLIENT_ID'] : $payments['paypal']['LIVE_CLIENT_ID'] }}&currency=EUR"></script>
    @endif
@endsection
