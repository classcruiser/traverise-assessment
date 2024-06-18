@extends('Classes.app')

@section('content')
    <div class="bg-neutral-100 min-h-screen">
        <div @class(['mx-auto', 'px-3', 'py-5', 'md:w-1/2' => $booking->is_cancelled, 'md:w-3/6' => !$booking->is_cancelled])>
            <div class="border border-neutral-300 rounded bg-white px-4 py-5">
                @if ($booking->is_cancelled)
                    <h2 class="text-2xl mb-3">Your booking has been cancelled</h2>
                    <p class="text-sm text-gray-600">Please contact us at <a href="mailto:{{ tenant('email') }}" title="" class="text-sm text-red-500 hover:text-red-600 font-semibold">{{ tenant('email') }}</a> if you need any assistance.</p>
                    <a href="{{ route('class.shop.index') }}" title="" class="text-sm text-red-500 hover:text-red-600 font-semibold">Return to Homepage</a>
                @else
                    <h2 class="text-xl mb-2">Invoice details</h2>
                    <table class="w-full font-extralight text-sm">
                        <thead>
                            <tr class="bg-neutral-50 border-y border-neutral-100">
                                <th class="uppercase px-1 py-3 text-left">Class</th>
                                <th class="uppercase px-1 py-3 text-left">Date</th>
                                <!--<th class="uppercase px-1 py-3 text-left">Instructor</th>-->
                                <th class="uppercase px-1 py-3 text-left">Guest</th>
                                <th class="uppercase px-1 py-3 text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->sessions as $session)
                                <tr class="border-b border-neutral-100 text-sm">
                                    <td class="p-2 text-danger-300">
                                        <b>{{ $session->session->category->short_name }} {{ $session->session->name }}</b>
                                    </td>
                                    <td class="p-2">{{ $session->date->format('l, d M y') }}, {{ $session->schedule->start_formatted }} - {{ $session->schedule->end_formatted }}</td>
                                    <!--<td class="p-2 text-left">{{ $session->instructor?->name ?? '-' }}</td>-->
                                    <td class="p-2 text-left">{{ $session->full_name }}</td>
                                    <td class="p-2 text-right font-bold">&euro; {{ $session->price }}</td>
                                </tr>
                            @empty
                                <tr class="border-b border-neutral-100">
                                    <td colspan="4" class="p-2 text-center">No class added</td>
                                </tr>
                            @endforelse
                            @forelse($booking->addons as $addon)
                                <tr class="border-b border-neutral-100">
                                    <td class="p-2">
                                        <i class="fa fa-gift fa-fw mr-1 text-danger-300 tippy" data-tippy-content="Extra / Addon"></i> {{$addon->addon->name}}
                                    </td>
                                    <td class="text-left p-2">
                                        @if($addon->addon->rate_type == 'Day')
                                            {{intVal($addon->amount)}} {{$addon->addon->unit_name}}
                                        @endif
                                    </td>
                                    <td class="text-center p-2">
                                        {{$addon->amount}} <i class="far fa-user"></i>
                                    </td>
                                    <td class="text-right p-2 font-bold">&euro; {{ $addon->price }}</td>
                                </tr>
                            @empty
                                <tr class="border-b border-neutral-100">
                                    <td colspan="4" class="text-center p-2">No addons added</td>
                                </tr>
                            @endforelse
                            <tr class="border-b border-neutral-100">
                                <td colspan="4" class="bg-neutral-50 px-2"></td>
                            </tr>
                            <tr>
                                <td class="text-right font-bold px-2 pb-1 pt-3" colspan="3">SUBTOTAL</td>
                                <td class="text-right font-bold px-2 pb-1 pt-3">&euro; {{(number_format($booking->total_price, 2))}}</td>
                            </tr>
                            @if ($taxes['cultural_tax_percent'] && $taxes['cultural_tax_percent'] > 0)
                                <tr>
                                    <td class="text-right font-bold px-2 py-1" colspan="3">{{$taxes['cultural_tax_percent']}}% CULTURAL TAX</td>
                                    <td class="text-right font-bold px-2 py-1">&euro;{{ $booking->room_tax }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="text-right font-bold px-2 py-1" colspan="3"><b>GRAND TOTAL</b></td>
                                <td class="text-right font-bold px-2 py-1">&euro;{{number_format($booking->grand_total, 2)}}</td>
                            </tr>
                            @if (($taxes['hotel_tax_percent'] && $taxes['hotel_tax_percent'] > 0) && ($taxes['goods_tax_percent'] && $taxes['goods_tax_percent'] > 0))
                                <tr>
                                    <td class="text-right font-bold px-2 py-1" colspan="3"><b>VAT</b></td>
                                    <td class="text-right font-bold px-2 py-1">{{$taxes['hotel_tax_percent']}}% &euro;{{number_format($taxes['hotel_tax'], 2)}}</td>
                                </tr>
                                <tr>
                                    <td class="text-right font-bold px-2 py-1" colspan="3">&nbsp;</td>
                                    <td class="text-right font-bold px-2 py-1">{{$taxes['goods_tax_percent']}}% &euro;{{number_format($taxes['goods_tax'], 2)}}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="text-right font-bold px-2 py-1 text-blue-500" colspan="3">TOTAL PAID</td>
                                <td class="text-right font-bold px-2 py-1 text-blue-500">&euro;{{number_format($booking->payment->total_paid, 2)}}</td>
                            </tr>
                            <tr class="hide-for-bank">
                                <td class="text-right font-bold px-2 py-1 text-danger" colspan="3">TOTAL TO PAY</td>
                                <td class="text-right font-bold px-2 py-1 text-danger">&euro;{{number_format($payment->open_balance, 2)}}</td>
                            </tr>
                            <tr class="hide-for-cc hidden">
                                <td class="text-right font-bold px-2 py-1 text-danger" colspan="3">TOTAL TO PAY</td>
                                <td class="text-right font-bold px-2 py-1 text-danger">&euro;{{number_format($payment->open_balance, 2)}}</td>
                            </tr>
                        </tbody>
                    </table>

                    <h2 class="text-xl mb-2">Payment Method</h2>
                    @if(session('error'))
                        <div class="alert is-error" data-kube="alert">
                            <i class="fa fa-exclamation-triangle"></i> {{session('error')}}
                            <span class="close is-small" data-type="close"></span>
                        </div>
                    @endif
                    @if(request()->has('cancel'))
                        <div class="alert is-notice" data-kube="alert">
                            <i class="fa fa-exclamation-triangle"></i> You have cancelled your payment
                            <span class="close is-small" data-type="close"></span>
                        </div>
                    @endif

                    <p class="text-sm mb-2">Please select one of payment method below:</p>

                    @if (!$booking->location->bank_transfer)
                        <div class="bg-red-200 text-red-500 rounded p-5 text-sm mb-5">
                            <p class="mb-2">Your booking is only completed and confirmed with your full payment, otherwise you booking will be deleted.</p>
                            <p>Erst mit vollständiger Zahlung ist ihre Buchung abgeschlossen und bestätigt. Ohne Bezahlung wird ihre Buchung gelöscht.</p>
                        </div>
                    @endif
                    <div class="flex gap-2 mt-3">
                        @if($booking->location->bank_transfer)
                            <a href="#payment-details" title="" class="payment-link" data-value="transfer">
                                <span><i class="fa fa-fw fa-money-bill-transfer text-neutral-600"></i></span>
                                <span class="uppercase text-sm text-red-600">bank transfer</span>
                            </a>
                        @endif
                        @if ($payments['paypal']['active'])
                            <a href="#payment-details" title="" class="payment-link" data-value="paypal">
                                <span><i class="fab fa-fw fa-paypal"></i></span>
                                <span class="uppercase text-sm text-blue-600">PayPal</span>
                            </a>
                        @endif
                        @if ($payments['stripe']['active'])
                            <a href="#payment-details" title="" class="payment-link creditcard-cc-button" data-value="creditcard" data-amount="{{ $payment->open_balance }}" data-payment-link="{{$payment->link}}">
                                <span>
                                    <i class="fa fa-credit-card fa-fw text-neutral-600"></i>
                                    <i class="fab fa-ideal fa-fw text-ideal"></i>
                                </span>
                                <span class="uppercase text-sm text-red-600">Credit Card & Other</span>
                            </a>
                            <div id="stripe_cs" class="hidden" data-value=""></div>
                        @endif
                    </div>

                    <div class="info-transfer info-box mt-4" id="payment-details">
                        {!! $booking->location->bank_transfer_text !!}
                        <div class="flex flex-col justify-center items-center mt-3">
                            <p class="button-info mb-2">Already made a payment?</p>
                            <button class="payment-transfer py-5 px-7 bg-red-500 hover:bg-red-600 text-white uppercase leading-none font-bold rounded" data-toggle="modal" data-target="#modal_transfer" data-type="banktransfer">Confirm Payment &nbsp;<i class="fal fa-angle-right"></i></button>
                        </div>
                    </div>

                    <div class="info-paypal info-box mt-4">
                        <div class="d-flex flex-column justify-content-center align-items-center mt-4">
                            <div id="paypal-button-container"></div>
                        </div>
                    </div>

                    <div class="info-creditcard info-box mt-4" id="payment-details">
                        <!-- Display a payment form -->
                        <form id="payment-form" data-success-url="{{ route('tenant.payment.class.thank_you', ['id' => $payment->link]) }}">
                            <div id="link-authentication-element">
                                <!--Stripe.js injects the Link Authentication Element-->
                            </div>
                            <div id="payment-element">
                                <!--Stripe.js injects the Payment Element-->
                            </div>
                            <button id="submit">
                                <div class="spinner hidden" id="spinner"></div>
                                <span id="button-text">Pay now</span>
                            </button>
                            <div id="payment-message" class="hidden"></div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="modal_transfer" class="relative z-10 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!--
            Background backdrop, show/hide based on modal state.

            Entering: "ease-out duration-300"
            From: "opacity-0"
            To: "opacity-100"
            Leaving: "ease-in duration-200"
            From: "opacity-100"
            To: "opacity-0"
        -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <form action="{{ route('tenant.payment.class.bank_transfer') }}" class="form-horizontal" enctype="multipart/form-data" method="post" id="transfer-form">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="border-b border-gray-300 pb-2">
                                <h2 class="text-base font-semibold leading-7 text-gray-900">Transfer Confirmation</h2>
                                <p class="text-sm leading-6 text-gray-600">INVOICE NUMBER: <b>{{ $payment->invoice }}</b></p>
                            </div>
                            <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-2">
                                <div class="col-span-full hideable">
                                    <label for="bank_name" class="block text-sm font-medium leading-6 text-gray-900">Bank Name <span class="text-danger">*</span> <span class="text-danger text-xs is-error err-bank_name"></span></label>
                                    <div class="mt-2">
                                        <input type="text" name="bank_name" id="bank_name" autocomplete="bank_name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-rose-600 sm:text-sm sm:leading-6">
                                    </div>
                                    <p class="mt-1 text-xs leading-6 text-gray-400">Put the bank complete name.</p>
                                </div>
                                <div class="col-span-full hideable">
                                    <label for="account_number" class="block text-sm font-medium leading-6 text-gray-900">Account Number <span class="text-danger">*</span> <span class="text-danger text-xs is-error err-account_number"></span></label>
                                    <div class="mt-2">
                                        <input type="text" name="account_number" id="account_number" autocomplete="account_number" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-rose-600 sm:text-sm sm:leading-6">
                                    </div>
                                    <p class="mt-1 text-xs leading-6 text-gray-400">Enter account number without spacing or separator.</p>
                                </div>
                                <div class="col-span-full hideable">
                                    <label for="iban_code" class="block text-sm font-medium leading-6 text-gray-900">IBAN / Swift Code</label>
                                    <div class="mt-2">
                                        <input type="text" name="iban_code" id="iban_code" autocomplete="iban_code" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-rose-600 sm:text-sm sm:leading-6">
                                    </div>
                                    <p class="mt-1 text-xs leading-6 text-gray-400">Check your IBAN / Swift code for your bank.</p>
                                </div>
                                <div class="col-span-full hideable">
                                    <label for="account_owner" class="block text-sm font-medium leading-6 text-gray-900">Account Owner <span class="text-danger">*</span> <span class="text-danger text-xs is-error err-account_owner"></span></label>
                                    <div class="mt-2">
                                        <input type="text" name="account_owner" id="account_owner" autocomplete="account_owner" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-rose-600 sm:text-sm sm:leading-6">
                                    </div>
                                    <p class="mt-1 text-xs leading-6 text-gray-400">Complete name of the owner.</p>
                                </div>
                                <div class="col-span-full">
                                    <label for="street-address" class="block text-sm font-medium leading-6 text-gray-900">Payment Date <span class="text-danger">*</span></label>
                                    <div class="my-2 grid grid-cols-4 gap-x-6">
                                        <div>
                                            <select name="date_day" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-rose-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                                @for($i = 1; $i <= 31; $i++)
                                                    <option value="{{str_pad($i, 2, '0', STR_PAD_LEFT)}}" {{$dates[2] == $i ? 'selected' : ''}}>{{str_pad($i, 2, '0', STR_PAD_LEFT)}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-span-2">
                                            <select name="date_month" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-rose-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                                @for($i = 1; $i <= 12; $i++)
                                                    <option value="{{$i}}" {{$dates[1] == $i ? 'selected' : ''}}>{{date('F', strtotime(date('Y-'. $i .'-d')))}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div>
                                            <select name="date_year" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-rose-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                                @for($i = (date('Y') - 1); $i <= (date('Y') + 1); $i++)
                                                    <option value="{{$i}}" {{$dates[0] == $i ? 'selected' : ''}}>{{$i}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <label for="proof" class="block text-sm font-medium leading-6 text-gray-900">Transfer Proof <span class="text-danger">*</span> <span class="text-danger text-xs is-error err-proof"></span></label>
                                    <div class="mt-2">
                                        <input type="file" name="proof" id="proof" autocomplete="proof" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-rose-600 sm:text-sm sm:leading-6">
                                    </div>
                                    <p class="mt-1 text-xs leading-6 text-gray-400">Attach transfer proof is mandatory. File must be in JPG format.</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            @csrf
                            <input type="hidden" name="origin" value="backend" />
                            <input type="hidden" name="payment_type" value="banktransfer" id="payment-type" />
                            <input type="hidden" name="payment_link" value="{{$payment->link}}" id="payment-link" />
                            <button type="submit" class="transfer-submit-button inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto" data-success-url="{{ route('tenant.payment.class.thank_you_bank') }}">Submit Confirmation</button>
                            <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    @if ($payments['paypal']['active'])
        <script src="https://www.paypal.com/sdk/js?client-id={{ $payments['paypal']['MODE'] == 'SANDBOX' ? $payments['paypal']['SANDBOX_CLIENT_ID'] : $payments['paypal']['LIVE_CLIENT_ID'] }}&currency=EUR"></script>
    @endif
    <script src="https://code.jquery.com/jquery-3.6.4.slim.min.js"></script>
    <script type="text/javascript">
        const sk = '{{ !$profile->test_mode ? env('STRIPE_LIVE_PUBLIC_KEY') : env('STRIPE_TEST_PUBLIC_KEY') }}';
        const account = '{{ !$profile->test_mode ? $profile->stripe_id : env('STRIPE_TEST_ACCOUNT') }}';
        var stripe = Stripe(sk, { stripeAccount: account });
        const paymentLink = '{{ $payment->link }}';
        const successUrl = '{{ route("tenant.payment.class.thank_you", ["id" => $payment->link]) }}';
        const paypalAmount = '{{ $payment->open_balance }}';
    </script>
    <script src="{{ asset('js/class-payment.js') }}"></script>
    <script type="text/javascript">
        @if (!$booking->location->bank_transfer)
            setTimeout(() => document.querySelector('.creditcard-cc-button').click(), 500);
        @endif
    </script>
@endsection
