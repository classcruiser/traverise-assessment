@extends('Booking.main') 

@section('content')
    <div class="page-content pt-4">
        <div class="content-wrapper container">
            <div class="content">
                <div class="row justify-content-center">
                    <div class="col-sm-7">
                        <div class="card">
                            <div class="card-body py-4 px-4">
                                <h2>Thank you for your payment.</h2>
                                <div class="payment--body is-inset-24">

                                    <p>
                                        You will receive a payment confirmation email within 24 hours after settlement. Feel free to contact our Customer Service Team <a href="mailto:{{tenant('email')}}" title="">here</a> if you have any questions.
                                    </p>

                                    <p><img src="/images/thank-you.jpg" class="img-fluid rounded" alt="" /></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection