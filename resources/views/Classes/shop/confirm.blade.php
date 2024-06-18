@extends('Classes.app')

@section('content')
    @include('Classes.shop.popup')

    <div id="stripe-popup-wrapper"
         class="w-screen h-screen bg-black bg-opacity-80 fixed top-0 left-0 overflow-y-auto z-[99999] flex justify-center items-center transition-all opacity-0 pointer-events-none">
        <div id="stripe-popup-content" class="block w-full md:w-1/3 mx-4 md:mx-0 rounded-sm relative">
            <a id="stripe-popup-close" href="javascript:" title="" class="absolute right-0 top-0 block p-4"><i
                    class="fal fa-times text-rose-600 text-lg"></i></a>
            <div class="stripe-form bg-white p-5 rounded-sm">
                <h2 class="mb-4">Enter your payment details</h2>
                <form id="payment-form"
                      data-success-url="{{ route('tenant.payment.class.thank_you', ['id' => $payment->link]) }}">
                    <div id="link-authentication-element">
                        <!--Stripe.js injects the Link Authentication Element-->
                    </div>
                    <div id="payment-element">
                        <!--Stripe.js injects the Payment Element-->
                    </div>
                    <button id="submit">
                        <div class="spinner hidden" id="spinner"></div>
                        <span id="button-text">PAY</span>
                    </button>
                    <div id="payment-message" class="hidden"></div>
                </form>
            </div>
        </div>
    </div>

    <div class="w-screen min-h-screen bg-gray-50 relative">
        <x-shop.container>
            <x-shop.header/>
            <x-shop.heading step="4">
                <div class="py-8 px-6">
                    @if (session()->has('message'))
                        <div
                            class="mb-6 p-4 bg-sky-50 border border-sky-100 text-sky-600 text-sm leading-relaxed">{{ session('message') }}</div>
                    @endif

                    <!-- create a 2 column grid -->
                    <div class="grid grid-cols-12 gap-0 md:gap-12">
                        <div class="col-span-12 md:col-span-6">
                            <h3 class="mb-4 text-lg font-bold">Class</h3>
                            @foreach ($session['guests'] as $index => $class)
                                <div class="py-1 flex justify-between items-start">
                                    <div class="max-w-[300px]">
                                        <span class="font-bold">{{ $index + 1 }}. {{ $class['class_name'] }}</span>
                                    </div>
                                    <span class="text-base">
                                        &euro; {{ $class['price'] }}
                                    </span>
                                </div>
                            @endforeach

                            @if ($session['addons']->count())
                                <h3 class="mb-4 text-lg font-bold mt-8 border-t pt-6 border-gray-200">Add ons</h3>
                                @foreach ($session['addons'] as $addon)
                                    <div class="py-1 flex justify-between items-start">
                                        <div>
                                            <span class="font-bold">{{ $addon['name'] }}</span> ({{ $addon['amount'] }}
                                            x)
                                        </div>
                                        <span class="text-base">
                                            &euro; {{ $addon['price'] * $addon['amount'] }}
                                        </span>
                                    </div>
                                @endforeach
                            @endif


                            <div class="py-1 mt-8 border-t pt-6 border-gray-200 w-full">
                                <span class="font-bold block mb-2">Voucher Code</span>
                                @if (isset($session['voucher']))
                                    <div class="flex justify-between items-start">
                                        <span class=""><b>{{ $session['voucher']['code'] }}</b> {{ $session['voucher']['name'] }} (<a
                                                href="#" title="" id="cancel_voucher" class="underline text-red-600">Cancel</a>)</span>
                                        <span class="text-base">
                                            {!! $session['voucher']['amount_type'] == 'VALUE' ? '- &euro;' : '' !!} {{ $session['voucher']['amount'] }}{!! $session['voucher']['amount_type'] == 'PERCENTAGE' ? '%' : '' !!}
                                        </span>
                                    </div>
                                @else
                                    <div class="flex justify-start w-full">
                                        <input type="text" name="voucher_code" id="voucher_code"
                                               class="border border-gray-200 rounded px-4 py-2 text-sm focus:outline-none focus:border-teal-400 flex-1"
                                               placeholder="Enter voucher code"/>
                                        <button
                                            class="bg-teal-100 text-teal-800 rounded-sm py-2 text-xs uppercase px-4 ml-2 hover:bg-teal-200 transition-all"
                                            id="apply_voucher">
                                            <span>Apply</span>
                                        </button>
                                    </div>
                                    <span id="voucher_result" class="hidden mt-2 text-sm block"></span>

                                    @if(!$guest)
                                        <span class="text-red-500 text-md block mt-2">For use voucher code you should login first</span>

                                        <div class="grid grid-cols-2 gap-2 mt-2">
                                            <a href="{{ route('guest.login', ['redirect' => URL(route('class.shop.confirm'))]) }}"
                                               title="">
                                                <button type="button"
                                                        class="block w-full bg-sky-400 text-sky-50 rounded py-3 px-5 font-bold hover:bg-sky-600 transition-all hover:text-sky-50">
                                                    Login
                                                </button>
                                            </a>
                                            <a href="{{ route('guest.register', ['redirect' => URL(route('class.shop.confirm'))]) }}"
                                               title="">
                                                <button type="button"
                                                        class="block w-full bg-sky-400 text-sky-50 rounded py-3 px-5 font-bold hover:bg-sky-600 transition-all hover:text-sky-50">
                                                    Register
                                                </button>
                                            </a>
                                        </div>
                                    @endif

                                    @if (session()->has('class.activation-code-message'))
                                        <span class="text-green-500 text-sm block mt-2">{{ session('class.activation-code-message') }}</span>
                                    @endif
                                @endif
                            </div>

                            @if($guest)
                                <div class="py-1 mt-8 border-t pt-6 border-gray-200 w-full">
                                    <span class="font-bold block mb-2">Multi Pass</span>

                                    @if (session()->has('class.multipass-credit'))
                                        <div class="flex justify-between items-start">
                                            <p>{{ $session['multipass-credit']['name'] ?? '-' }}. Remaining:
                                                &euro; {{ $session['multipass-credit']['value'] }}</p>
                                            <span>(<a href="#" title="" id="cancel_pass" class="underline text-red-600">Cancel</a>)</span>
                                        </div>
                                    @elseif (session()->has('class.multipass-session'))
                                        <div class="flex justify-between items-start">
                                            <p>{{ $session['multipass-session']['name'] ?? '-' }}.
                                                Remaining: {{ $session['multipass-session']['remaining'] }} session</p>
                                            <span>(<a href="#" title="" id="cancel_pass" class="underline text-red-600">Cancel</a>)</span>
                                        </div>
                                    @else
                                        @if ($passes_total > 0)
                                            <p class="text-sm">You have {{ $passes_total }} active passes left. You can use
                                                them to book classes.</p>
                                            <select name="class_multi_pass_id"
                                                    class="mt-2 rounded-sm border-gray-200 text-xs py-3 px-4 w-full"
                                                    id="multi_pass">
                                                <option>Select a pass</option>
                                                @if($passes)
                                                    @foreach ($passes as $pass)
                                                        @if ($pass['multi_pass']['type'] == 'SESSION' && $pass['remaining'] < count($session['guests']))
                                                            <option value="{{ $pass['id'] }}"
                                                                    disabled>{{ $pass['multi_pass']['name'] }} -
                                                                REMAINING: {{ $pass['remaining_text'] }}</option>
                                                        @else
                                                            <option
                                                                value="{{ $pass['id'] }}">{{ $pass['multi_pass']['name'] }}
                                                                -
                                                                REMAINING: {{ $pass['remaining_text'] }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                        @else
                                            <p class="text-sm">You don't have any active pass. You may purchase a multi pass <a href="{{ route('multi-pass.index') }}" target="_blank" class="underline text-teal-400 font-bold">here</a></p>
                                        @endif
                                    @endif
                                </div>
                            @endif

                            <div class="py-1 flex justify-between items-start mt-8 border-t pt-6 border-gray-200">
                                <div>
                                    <span class="text-base">Subtotal</span>
                                </div>
                                <span class="text-base">
                                &euro; {{ \App\Services\Classes\ShopService::getSubtotalPrice() }}
                            </span>
                            </div>
                            @if ($session['addons']->count())
                                <div class="py-1 flex justify-between items-start border-gray-200">
                                    <div>
                                        <span class="text-base">Add ons</span>
                                    </div>
                                    <span class="text-base">
                                    &euro; {{ \App\Services\Classes\ShopService::getTotalAddonsPrice() }}
                                </span>
                                </div>
                            @endif
                            @if (isset($session['voucher']))
                                <div class="py-1 flex justify-between items-start border-gray-200">
                                    <div>
                                        <span class="text-base">Discount</span>
                                    </div>
                                    <span class="text-base">
                                    - &euro; {{ \App\Services\Classes\ShopService::getDiscountValue() }}
                                </span>
                                </div>
                            @endif
                            @if (isset($session['multipass-credit']))
                                <div class="py-1 flex justify-between items-start border-gray-200">
                                    <div>
                                        <span class="text-base">Multi Pass credit</span>
                                    </div>
                                    <span class="text-base">
                                    - &euro; {{ $session['multipass-credit']['type'] == 'PERCENTAGE' ? number_format($session['multipass-credit']['value']) : ($session['multipass-credit']['value']) }}
                                </span>
                                </div>
                            @endif
                            @if (isset($session['multipass-session']))
                                <div class="py-1 flex justify-between items-start border-gray-200">
                                    <div>
                                        <span class="text-base">Multi Pass session ({{ \App\Services\Classes\ShopService::getTotalEligibleMultiPassSession() }} session)</span>
                                    </div>
                                    <span class="text-base">
                                    - &euro; {{ number_format($discount) }}
                                </span>
                                </div>
                            @endif
                            <div class="py-1 flex justify-between items-start border-gray-200">
                                <div>
                                    <span class="font-bold text-lg">Total</span>
                                </div>
                                <span class="text-base font-bold">
                                &euro; {{ $total_price }}
                            </span>
                            </div>
                            @if ($total_price > 0)
                                <div class="py-1">
                                <span class="text-xs">
                                    Price contains <b>&euro; {{$tax_info['vat']}}</b> VAT
                                    (<b>&euro; {{number_format($taxes['goods_tax'], 2)}}</b> {{$taxes['goods_tax_percent']}}%)
                                </span>
                                </div>
                            @endif
                        </div>
                        <div class="col-span-12 md:col-span-6 mt-4 md:mt-0">
                            <h3 class="mb-4 text-lg font-bold">Booker details</h3>

                            <p class="mb-2">
                                <span
                                    class="font-bold text-base">{{ $session['booker']['first_name'] }} {{ $session['booker']['last_name'] }}</span>
                                <br>
                                {{ $session['booker']['email'] }}
                                <br>
                                {{ $session['booker']['phone'] }}
                            </p>
                            <p>
                                <span class="font-bold">Address</span>
                                <br/>
                                {{ $session['booker']['address'] }}, {{ $session['booker']['country'] }}
                            </p>

                            @if (!$session['booker_only'])
                                <h3 class="my-4 text-lg font-bold border-t border-gray-200 pt-4">Guest details</h3>
                                <div class="">
                                    @foreach ($session['guests'] as $index => $class)
                                        <div class="py-1 flex justify-start items-start">
                                        <span class="font-bold">
                                            {{ $index + 1 }}. {{ $class['first_name'] .' '. $class['last_name'] }} (<span
                                                class="text-gray-500">{{ $class['weight']}} kg</span>)
                                        </span>
                                            <span class="text-gray-500 ml-2">{{ $class['email']}}</span>
                                        </div>
                                        <div class="pb-4">
                                            {{ $class['class_name'] }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if ($total_price > 0)
                                <h3 class="my-4 text-lg font-bold border-t border-gray-200 pt-4">Payment methods</h3>
                                <div class="flex justify-start gap-4" id="payment-methods">
                                    <a href="#" title=""
                                       class="rounded-sm py-5 px-6 border border-teal-300 shadow flex flex-col justify-start items-center space-y-2 uppercase text-xs min-w-[120px]"
                                       data-method="stripe"
                                       data-success-url="{{ route('tenant.payment.class.thank_you', ['id' => $payment->link]) }}"
                                       data-amount="{{ $total_price }}"
                                       data-payment-link="{{ $payment->link }}"
                                    >
                                        <i class="fa fa-credit-card fa-fw block fa-2x text-teal-300"></i>
                                        <span>Credit Card</span>
                                    </a>
                                    @if ($payment_methods->where('name', 'Paypal')->count())
                                        <a href="#" title=""
                                           class="rounded-sm py-5 px-6 border border-gray-200 flex flex-col justify-start items-center space-y-2 uppercase text-xs min-w-[120px]"
                                           data-method="paypal">
                                            <i class="fab fa-cc-paypal fa-fw block fa-2x text-gray-500"></i>
                                            <span>PayPal</span>
                                        </a>
                                    @endif
                                </div>
                            @endif

                        </div>
                    </div>

                    @if ($total_price > 0)
                        <div class="mt-16 flex justify-between">
                            <x-shop.button href="/book-class/profile" style="secondary">
                                <i class="ml-1 fal fa-arrow-left"></i> PREVIOUS
                            </x-shop.button>
                            <x-shop.button type="button" :btn="['type' => 'submit', 'name' => 'submit']" style="primary"
                                           id="confirm">
                                <span>CONFIRM</span>
                            </x-shop.button>
                            <div id="paypal-button-container" class="hidden"></div>
                            <input type="hidden" name="methods" value="stripe" id="methods"/>
                        </div>

                        <div id="stripe_cs" class="hidden" data-value=""></div>
                    @else
                        <div class="mt-16 flex justify-between">
                            <x-shop.button href="/book-class/profile" style="secondary">
                                <i class="ml-1 fal fa-arrow-left"></i> PREVIOUS
                            </x-shop.button>
                            <x-shop.button type="button" :btn="['type' => 'submit', 'name' => 'submit']" style="primary"
                                           id="confirm">
                                <span>CONFIRM</span>
                            </x-shop.button>
                            <div id="paypal-button-container" class="hidden"></div>
                            <input type="hidden" name="methods" value="voucher" id="methods"/>
                        </div>
                    @endif
                    <input type="hidden" name="origin" value="shop" id="origin"/>
                </div>
            </x-shop.heading>
        </x-shop.container>
    </div>
@endsection

@section('scripts')
    <script
        src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.mode') == 'sandbox' ? config('paypal.sandbox.client_id') : config('paypal.live.client_id') }}&currency=EUR"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.slim.min.js"></script>
    <script type="text/javascript">
        const sk = window.sk = '{{ !$profile->test_mode ? config('stripe.live_public_key') : config('stripe.test_public_key') }}';
        const acc = window.acc = '{{ !$profile->test_mode ? $profile->stripe_id : env('STRIPE_TEST_ACCOUNT') }}';
        var stripe = Stripe(window.sk, {
            stripeAccount: acc
        });

        paypal.Buttons({
            style: {
                layout: 'horizontal',
                color: 'blue',
                shape: 'pill',
                label: 'pay',
                height: 37
            },

            // Call your server to set up the transaction
            createOrder: function (data, actions) {
                return axios.post(route('tenant.payment.paypal.create-order'), {
                    type: 'class',
                    payment_link: '{{ $payment->link }}',
                }).then(res => {
                    console.log(res.data.id)
                    return res.data.id;
                });
            },

            // Call your server to finalize the transaction
            onApprove: function (data, actions) {
                return axios.post(route('tenant.payment.paypal.capture-order'), {
                    order_id: data.orderID,
                }).then(function (res) {
                    const orderData = res.data;
                    var errorDetail = Array.isArray(orderData.details) && orderData.details[0];

                    if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
                        return actions.restart();
                    }

                    if (errorDetail) {
                        var msg = 'Sorry, your transaction could not be processed.';
                        if (errorDetail.description) msg += '\n\n' + errorDetail.description;
                        if (orderData.debug_id) msg += ' (' + orderData.debug_id + ')';
                        return alert(msg); // Show a failure message (try to avoid alerts in production environments)
                    }

                    actions.redirect('{{ route('class.shop.thank-you') }}');
                });
            }
        }).render('#paypal-button-container');
    </script>
@endsection
