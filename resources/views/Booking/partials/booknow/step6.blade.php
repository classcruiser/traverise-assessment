<div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">6 - Enjoy your trip</div>
@if ($step == 6)
    <div class="section-body {{ $step == 6 ? 'active' : '' }} font-size-lg">
        @if ($booking_status == 'CONFIRMED')
        	<h3>Congratulations</h3>
        	<p>We have received your booking. You will receive an email confirmation shortly. Your booking reference number is #<b>{{ $ref }}</b>.</p>
        	<p>We look forward to your stay with us!</p>
        @else
        	<h3>Thanks for submitting your booking request</h3>
        	<p>Our team will review your request and contact you within 24 hours. Your booking reference number is #<b>{{ $ref }}</b>.</p>
        	<p>Thank you for your patience!</p>
        @endif

        <p>If you have any questions feel free to send us an email to <a href="mailto:{{ $profile->contact_email }}" title="" class="text-custom"><b>{{ $profile->contact_email }}</b></a></p>
    </div>

    @section('gtagscripts')
        @if ($profile->google_ecomm_tag)
            <script>
            window.dataLayer = window.dataLayer || [];
                dataLayer.push({ ecommerce: null });  // Clear the previous ecommerce object.
                dataLayer.push({
                    event: "purchase",
                    ecommerce: {
                        transaction_id: '{{ $ref }}',
                        affiliation: '{{ $profile->title }}',
                        value: {{ intVal($booking->payment->total) }},
                        tax: 0,
                        shipping: 0,
                        currency: 'EUR',
                        items: [
                            @foreach ($booking->rooms as $room)
                            {
                                item_id: '{{ $room->id }}',
                                item_name: '{{ $room->room->name }}',
                                item_category: 'Room',
                                price: {{ floatVal($room["price"]) }},
                                quantity: 1,
                                currency: 'EUR',
                            },
                            @if ($room->addons())
                                @foreach ($room->addons as $addon)
                                {
                                    item_id: '{{ $addon->extra_id }}',
                                    item_name: '{{ $addon->details->name }} x{{ $room["amount"] }}',
                                    item_category: 'Addons',
                                    price: {{ floatVal($addon["price"]) }},
                                    quantity: 1,
                                    currency: 'EUR',
                                },
                                @endforeach
                            @endif
                            @endforeach
                            @foreach ($booking->transfers as $transfer)
                            {
                                item_id: '{{ $transfer->transfer_extra_id }}',
                                item_name: '{{ $transfer->details->name }}',
                                item_category: 'Transfers',
                                price: {{ floatVal($transfer["price"]) }},
                                quantity: 1,
                                currency: 'EUR',
                            },
                            @endforeach
                        ]
                    },
                });
            </script>
        @endif
    @endsection

@endif
