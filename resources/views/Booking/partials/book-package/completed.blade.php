<div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">4 - Enjoy your trip</div>
@if ($step == 4)
    <div class="section-body {{ $step == 4 ? 'active' : '' }} font-size-lg">
        @if ($booking->status == 'CONFIRMED')
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
@endif
