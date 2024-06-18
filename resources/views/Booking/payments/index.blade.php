@extends('Booking.main')

@section('customcss')
<style>
    .Label {
        display: block;
        text-transform: uppercase !important;
        font-size: 0.9em !important;
    }
</style>
@endsection

@php
    $ext_tax = 0;
@endphp

@section('content')
    <div id="modal_transfer" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-grey-800">
                    <h5 class="modal-title">Transfer Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="javascript:" class="form-horizontal" enctype="multipart/form-data" method="post" id="transfer-form">
                    <div class="modal-body">
                        <p>INVOICE NUMBER: <b>{{$payment->invoice_number}}</b></p>
                        <br />
                        <div class="form-group row hideable">
                            <label class="col-form-label col-sm-3">Bank Name <span class="is-req">*</span> <span class="is-error err-bank_name"></span></label>
                            <div class="col-sm-9">
                                <input type="text" name="bank_name" class="form-control form-control-sm" />
                                <span class="form-text text-muted">Put the bank complete name.</span>
                            </div>
                        </div>
                        <div class="form-group row hideable">
                            <label class="col-form-label col-sm-3">Account Number <span class="is-req">*</span> <span class="is-error err-account_number"></span></label>
                            <div class="col-sm-9">
                                <input type="text" name="account_number" class="form-control form-control-sm" />
                                <span class="form-text text-muted">Enter account number without spacing or separator.</span>
                            </div>
                        </div>
                        <div class="form-group row hideable">
                            <label class="col-form-label col-sm-3">IBAN / Swift Code</label>
                            <div class="col-sm-9">
                                <input type="text" name="iban_code" class="form-control form-control-sm" />
                                <span class="form-text text-muted">Check your IBAN / Swift code for your bank.</span>
                            </div>
                        </div>
                        <div class="form-group row hideable">
                            <label class="col-form-label col-sm-3">Account Owner <span class="is-req">*</span> <span class="is-error err-account_owner"></span></label>
                            <div class="col-sm-9">
                                <input type="text" name="account_owner" class="form-control form-control-sm" />
                                <span class="form-text text-muted">Complete name of the owner.</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">Payment Date <span class="is-req">*</span></label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-3">
                                        <select name="date_day" class="form-control form-control-sm">
                                            @for($i = 1; $i <= 31; $i++)
                                                <option value="{{str_pad($i, 2, '0', STR_PAD_LEFT)}}" {{$dates[2] == $i ? 'selected' : ''}}>{{str_pad($i, 2, '0', STR_PAD_LEFT)}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <select name="date_month" class="form-control form-control-sm">
                                            @for($i = 1; $i <= 12; $i++)
                                                <option value="{{$i}}" {{$dates[1] == $i ? 'selected' : ''}}>{{date('F', strtotime(date('Y-'. $i .'-d')))}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <select name="date_year" class="form-control form-control-sm">
                                            @for($i = (date('Y') - 1); $i <= (date('Y') + 1); $i++)
                                                <option value="{{$i}}" {{$dates[0] == $i ? 'selected' : ''}}>{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">Transfer Proof <span class="is-req">*</span> <span class="is-error err-proof"></span></label>
                            <div class="col-sm-9">
                                <input type="file" name="proof" class="form-control" />
                                <span class="form-text text-muted">Attach transfer proof is mandatory. File must be in JPG format.</span>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        @csrf
                        <input type="hidden" name="payment_type" value="banktransfer" id="payment-type" />
                        <input type="hidden" name="payment_link" value="{{$payment->link}}" id="payment-link" />
                        <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Cancel and close</button>
                        <button type="submit" class="btn bg-danger transfer-submit-button">Submit Confirmation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                <div class="row justify-content-center">
                    <div class="col-sm-10">
                        @if($payment->booking->status == 'CANCELLED')
                            <div class="card">
                                <div class="card-body">
                                    <h2 class="mb-3">Your booking has been cancelled</h2>
                                    <p>Please contact us at <a href="mailto:info@kimasurf.com" title="" class="text-kima"><b>info@kimasurf.com</b></a> if you need any assistance.</p>

                                    <p class="mb-0"><a href="https://kimasurf.com" title="" class="text-kima"><b>Return to Homepage</b></a></p>
                                </div>
                            </div>
                        @else
                            <div class="card">
                                <div class="card-body">
                                    <h2>Invoice details</h2>
                                    <div class="mobile-overflow">
                                        <table class="table table-xs">
                                            <thead>
                                                <tr class="alpha-grey border-top-1 border-alpha-grey border-bottom-1">
                                                    <th class="text-uppercase p-3">Product/Package</th>
                                                    <th class="text-uppercase p-3" style="min-width: 100px;">Bed Type</th>
                                                    <th class="text-uppercase p-3" style="min-width: 170px;">Stay Dates</th>
                                                    <th class="text-uppercase p-3">Duration</th>
                                                    <th class="text-uppercase p-3 text-left" style="min-width: 100px;">Unit</th>
                                                    <th class="text-uppercase p-3 text-right" style="min-width: 100px;">Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($booking->rooms_count)
                                                    @foreach($booking->rooms as $r)
                                                        <tr>
                                                            <td>
                                                                <b class="text-danger">{{$r->subroom->name}}</b> {!! $r->is_private ? '(Private)' : '' !!}
                                                            </td>
                                                            <td>{{$r->bed_type}}</td>
                                                            <td>{{date('d.m.y', strtotime($r->from))}} - {{date('d.m.y', strtotime($r->to))}}</td>
                                                            <td>{{$r->days}} days / {{$r->nights}} nights</td>
                                                            <td class="text-left">{{$r->guest}} {{Str::plural('guest', $r->guest)}}</td>
                                                            <td class="text-right"><b>&euro; {{$r->price}}</td>
                                                        </tr>
                                                        @if($booking->location->duration_discount > 0)
                                                            <tr>
                                                                <td colspan="5">&rsaquo; Duration Discount</td>
                                                                <td class="text-right"><b>- &euro; {{floatVal($r->duration_discount)}}</b></td>
                                                            </tr>
                                                        @endif
                                                        @if($r->discounts())
                                                            @foreach($r->discounts as $offer)
                                                                <tr>
                                                                    <td colspan="5">
                                                                        &rsaquo; Special Offer:
                                                                        {{$offer->offer->name}} ({!! $offer->offer->discount_type == 'Percent' ? $offer->offer->discount_value .'%' : '&euro;'. $offer->offer->discount_value !!})
                                                                    </td>
                                                                    <td class="text-right"><b>- &euro; {{floatVal($offer->discount_value)}}</b></td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        @if($r->addons->count() > 0)
                                                            @foreach($r->addons as $addon)
                                                                <tr>
                                                                    <td colspan="3">&rsaquo; {{$addon->details->name}}</td>
                                                                    <td class="text-left">{{$addon->details->is_flexible ? $addon->amount .' days' : ''}}</td>
                                                                    <td class="text-left">{{$addon->guests}} {{$addon->details->unit_name}}</td>
                                                                    <td class="text-right"><b>&euro; {{$addon->price}}</b></td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="7"><em>No room</em></td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td class="alpha-grey p-0" colspan="7" style="height: 7px;"></td>
                                                </tr>
                                                @if($booking->transfers->count() > 0)
                                                    @foreach($booking->transfers as $transfer)
                                                        <tr>
                                                            <td colspan="4">
                                                                &rsaquo; {{$transfer->details->name}}
                                                                {!! $transfer->flight_detail !!}
                                                            </td>
                                                            <td class="text-center">{{$booking->total_guests}}</td>
                                                            <td class="text-right"><b>&euro; {{$transfer->price}}</b></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                <tr>
                                                    <td class="text-right pt-3" colspan="5"><b>SUBTOTAL</b></td>
                                                    <td class="text-right pt-3"><b>&euro; {{(number_format($booking->total_price, 2))}}</b></td>
                                                </tr>
                                                @if($booking->discounts)
                                                    @foreach($booking->discounts as $disc)
                                                        <tr>
                                                            <td class="text-right border-0 py-1" colspan="5"><b>DISCOUNT</b></td>
                                                            <td class="text-right border-0 py-1">
                                                                @if($disc->type == 'Percent')
                                                                    @if($disc->apply_to == 'ALL')
                                                                        <b>- &euro; {{(number_format($booking->total_price * ($disc->value / 100), 2))}}</b>
                                                                    @else
                                                                        <b>- &euro; {{(number_format($booking->subtotal * ($disc->value / 100), 2))}}</b>
                                                                    @endif
                                                                @else
                                                                    <b>- &euro; {{(number_format($disc->value, 2))}}</b>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                @if ($tax['exclusives']['total'] && count($tax['exclusives']['taxes']))
                                                    @foreach ($tax['exclusives']['taxes'] as $tax)
                                                        @php
                                                            $ext_amount = \App\Services\Booking\TaxService::calculateExclusiveTax($booking->subtotal_with_discount, $tax->rate, $tax->type);
                                                            $ext_tax += $ext_amount;
                                                        @endphp
                                                        <tr>
                                                            <td class="text-right border-0" colspan="5"><b>{{ strtoupper($tax->name) }} ({{ number_format($tax->rate, 0) }}%)</b></td>
                                                            <td class="text-right border-0">
                                                                <b>&euro;{{ parsePrice($ext_amount) }}</b>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                <tr>
                                                    <td class="text-right border-0" colspan="5"><b>GRAND TOTAL*</b></td>
                                                    <td class="text-right border-0"><b>&euro; {{number_format($booking->grand_total, 2)}}</b></td>
                                                </tr>
                                                @if($booking->payment->open_balance > 0 && $booking->payment->records_count > 0)
                                                    <tr>
                                                        <td class="text-right border-0 py-1" colspan="5"><b>TOTAL PAID</b></td>
                                                        <td class="text-right border-0 py-1"><b>&euro; {{ number_format($booking->payment->total_paid, 2) }}</b></td>
                                                    </tr>
                                                @endif
                                                @if ($booking->payment->open_balance > 0)
                                                    <tr class="hide-for-bank">
                                                        <td class="text-right border-0 py-1" colspan="5"><b>PAYMENT PROCESSING FEE</b></td>
                                                        <td class="text-right border-0 py-1">
                                                            <b>&euro; {{(number_format($payment->processing_fee, 2))}}</b>
                                                        </td>
                                                    </tr>
                                                    @if($booking->status != 'DRAFT' && $booking->payment->status == 'DUE' && $booking->location->enable_deposit)
                                                        <tr class="hide-for-cc">
                                                            <td class="text-right border-0 py-1" colspan="5"><b>DEPOSIT (DUE {{$booking->deposit_expiry->format('d.m.Y H:i')}})</b></td>
                                                            <td class="text-right border-0 py-1">
                                                                <b>&euro; {{$booking->deposit_amount}}</b>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr class="hide-for-bank">
                                                        <td class="text-right border-0 py-1 text-danger" colspan="5"><b>TOTAL TO PAY</b></td>
                                                        <td class="text-right border-0 py-1 text-danger"><b>&euro; {{(number_format($booking->payment->open_balance_with_fee, 2))}}</b></td>
                                                    </tr>
                                                    <tr class="hide-for-cc hidden">
                                                        <td class="text-right border-0 py-1 text-danger" colspan="5"><b>TOTAL TO PAY</b></td>
                                                        <td class="text-right border-0 py-1 text-danger"><b>&euro; {{(number_format($booking->payment->open_balance, 2))}}</b></td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>

                                        @if($booking->location->goods_tax && $booking->location->hotel_tax)
                                            <div style="text-align: right" class="mt-2 pr-3 hide-for-bank">{!! $tax_info_cc !!}</div>
                                            <div style="text-align: right" class="mt-2 pr-3 hide-for-cc hidden">{!! $tax_info_bank !!}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body border-0">
                                    <h2>Payment Method</h2>
                                    <div class="payment--body is-inset-24">
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

                                        <p>Please select one of payment method below:</p>

                                        @if (!$booking->location->bank_transfer)
                                            <div class="alert alert-danger border-0 alert-dismissible fade show">
                                                <p>Your booking is only completed and confirmed with your full payment, otherwise you booking will be deleted.</p>
                                                <p class="mb-0">Erst mit vollständiger Zahlung ist ihre Buchung abgeschlossen und bestätigt. Ohne Bezahlung wird ihre Buchung gelöscht.</p>
                                            </div>
                                        @endif
                                        <div class="payment--buttons payment mt-3">
                                            @if($booking->location->bank_transfer)
                                                <a href="#payment-details" title="" class="payment--link" data-value="transfer">
                                                    <span><i class="fa fa-fw fa-money-bill-transfer"></i></span>
                                                    bank transfer
                                                </a>
                                            @endif
                                            @if ($payment_methods->where('name', 'Paypal')->count())
                                                <a href="#payment-details" title="" class="payment--link" data-value="paypal">
                                                    <span><i class="fab fa-fw fa-paypal"></i></span>
                                                    PayPal
                                                </a>
                                            @endif
                                            @if ($payment_methods->where('name', 'Stripe')->count())
                                                <a href="#payment-details" title="" class="payment--link creditcard-cc-button" data-value="creditcard">
                                                    <span>
                                                        <i class="fa fa-credit-card fa-fw"></i>
                                                        <i class="fab fa-ideal fa-fw text-ideal"></i>
                                                    </span>
                                                    Credit Card & Other
                                                </a>
                                                <div id="stripe_cs" class="d-none" data-value=""></div>
                                            @endif
                                        </div>

                                        <div class="info-transfer info-box mt-3" id="payment-details">
                                            {!! $booking->location->bank_transfer_text !!}
                                            <div class="d-flex flex-column justify-content-center align-items-center mt-3">
                                                <p class="button-info">Already made a payment?</p>
                                                <button class="payment-button payment-transfer" data-toggle="modal" data-target="#modal_transfer" data-type="banktransfer">Confirm Payment &nbsp;<i class="fal fa-angle-right"></i></button>
                                            </div>
                                        </div>

                                        <div class="info-paypal info-box mt-3">
                                            <div class="d-flex flex-column justify-content-center align-items-center mt-3">
                                                <div id="paypal-button-container"></div>
                                            </div>
                                        </div>

                                        <div class="info-creditcard info-box mt-3" id="payment-details">
                                            <!-- Display a payment form -->
                                            <form id="payment-form">
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
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
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
<script src="{{asset('js/payment.js')}}"></script>
<script type="text/javascript">
    @if(!$booking->location->bank_transfer && !$payments['paypal']['active'])
        setTimeout(() => document.querySelector('.creditcard-cc-button').click(), 500);
    @endif
    tippy('.tippy', {
        content(reference) {
            const id = reference.getAttribute('data-template')
            const template = document.getElementById(id)
            return template.innerHTML
        },
        arrow: true,
        allowHTML: true,
    });

    const sk = '{{ !$profile->test_mode ? config('stripe.live_public_key') : config('stripe.test_public_key') }}';
    const acc = '{{ !$profile->test_mode ? $profile->stripe_id : env('STRIPE_TEST_ACCOUNT') }}';
    var checkoutButton = $('.creditcard-cc-button');
    var stripe = Stripe(sk, {
        stripeAccount: acc
    });

    document.querySelector("#payment-form").addEventListener("submit", handleSubmit);

    let emailAddress = '';
    let currency = '';

    // STRIPE
    checkoutButton.on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var btnHtml = $this.html();
        let $container = $('#stripe_cs');
        let cs = $container.attr('data-value');
        const stripeForm = $('#payment-form');

        stripeForm.addClass('d-block').removeClass('d-none');

        if (cs != '') {
            return false;
        }

        // Get client secret
        axios.post('/client_secret', {
            currency: currency,
            amount: {{$payment->total}},
            payment_link: '{{$payment->link}}',
        }).then(({data}) => {

            $container.attr('data-value', data.clientSecret);
            const opt = {
                clientSecret: data.clientSecret,
                appearance: {
                    theme: 'stripe',
                    variables: {
                        colorPrimary: '#fd5b60',
                        spacingGridRow: '14px',
                        spacingGridColumn: '14px',
                    },
                    rules: {
                        '.Label': {
                            marginBottom: '6px',
                            marginTop: '12px',
                            textTransform: 'uppercase',
                            fontSize: '12px'
                        },
                        '.Tab': {
                            marginTop: '12px'
                        }
                    }
                }
            }
            elements = stripe.elements(opt);

            const linkAuthenticationElement = elements.create("linkAuthentication");
            linkAuthenticationElement.mount("#link-authentication-element");

            const paymentElementOptions = {
                layout: "tabs",
            };

            const paymentElement = elements.create("payment", paymentElementOptions);
            paymentElement.mount("#payment-element");

        }).catch(err => {
            // Handle error here if needed
        })
    })

    // PAYPAL
    paypal.Buttons({
        style: {
            layout: 'horizontal',
            color:  'blue',
            shape:  'pill',
            label:  'pay',
            height: 37
        },

        // Call your server to set up the transaction
        createOrder: function(data, actions) {
            return axios.post(route('tenant.payment.paypal.create-order'), {
                type: 'booking',
                payment_link: '{{ $booking->payment->link }}',
            }).then(res => {
                return res.data.id;
            });
        },

        // Call your server to finalize the transaction
        onApprove: function(data, actions) {
            return axios.post(route('tenant.payment.paypal.capture-order'), {
                order_id: data.orderID,
            }).then(function(res) {
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

                actions.redirect('{{ route('tenant.payment.thank-you', ['id' => $booking->payment->link]) }}');
            });
        }
    }).render('#paypal-button-container');

    // Handle payment
    async function handleSubmit(e) {
        e.preventDefault();
        setLoading(true);


        const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
            // Make sure to change this to your payment completion page
            return_url: "{{route('tenant.payment.thank-you', ['id' => $payment->link])}}",
            receipt_email: emailAddress,
            },
        });

        // This point will only be reached if there is an immediate error when
        // confirming the payment. Otherwise, your customer will be redirected to
        // your `return_url`. For some payment methods like iDEAL, your customer will
        // be redirected to an intermediate site first to authorize the payment, then
        // redirected to the `return_url`.
        if (error.type === "card_error" || error.type === "validation_error") {
            showMessage(error.message);
        } else {
            showMessage("An unexpected error occurred.");
        }

        setLoading(false);

        // Fetches the payment intent status after payment submission
        async function checkStatus() {
            const clientSecret = new URLSearchParams(window.location.search).get(
                "payment_intent_client_secret"
            );

            if (!clientSecret) {
                return;
            }

            const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

            switch (paymentIntent.status) {
                case "succeeded":
                    showMessage("Payment succeeded!");
                break;
                case "processing":
                    showMessage("Your payment is processing.");
                break;
                case "requires_payment_method":
                    showMessage("Your payment was not successful, please try again.");
                break;
                default:
                    showMessage("Something went wrong.");
                break;
            }
        }

        checkStatus();

        // ------- UI helpers -------
        function showMessage(messageText) {
            const messageContainer = document.querySelector("#payment-message");
            messageContainer.classList.remove("hidden");
            messageContainer.textContent = messageText;

            setTimeout(function () {
                messageContainer.classList.add("hidden");
                messageText.textContent = "";
            }, 4000);
        }

        // Show a spinner on payment submission
        function setLoading(isLoading) {
            if (isLoading) {
                // Disable the button and show a spinner
                document.querySelector("#submit").disabled = true;
                document.querySelector("#spinner").classList.remove("hidden");
                document.querySelector("#button-text").classList.add("hidden");
            } else {
                document.querySelector("#submit").disabled = false;
                document.querySelector("#spinner").classList.add("hidden");
                document.querySelector("#button-text").classList.remove("hidden");
            }
        }
    }
</script>
@endsection
