@extends('Booking.app')

@section('content')
<div id="modal_info" class="modal fade modal_scanner" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-grey-800">
                <h5 class="modal-title">Booking details</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body" id="result">&nbsp;</div>

            <div class="modal-footer">
                <button class="btn bg-danger scan-again">PROCEED</button>
            </div>

        </div>
    </div>
</div>

<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Scanner</span>
            <span class="breadcrumb-item active">Check In</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="row">
                <div class="offset-lg-3 col-sm-12 col-lg-6">
                    <div class="card">
                        <div class="card-header bg-light font-bold">
                            Scan QR code
                        </div>
                        <div class="card-body p-0" id="qr-area"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const html5QrCode = new Html5Qrcode("qr-area");
    const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        html5QrCode.pause();

        /* handle success */
        let url = decodedText;

        if (v.startsWith(url, '{{ url('/') }}')) {
            axios.get(url +'?checkin').then(res => {
                // show a popup
                const { data } = res;
                if (v.countSubstrings(data, 'input')) {
                    showPopup(data);
                } else {
                    const audio = new Audio('{{ url("sounds/error.wav") }}');
                    audio.play();

                    swal({
                        text: "There is no guest need to be processed!",
                        icon: "error",
                        button: "Rescan",
                    }).then(() => {
                        resumeQRScanner();
                    });
                }
            }).catch(function (error) {
                if (error.response) {
                    console.log(error.response.data);
                    console.log(error.response.status);
                } else if (error.request) {
                    console.log(error.request);
                } else {
                    console.log('Error', error.message);
                }

                const audio = new Audio('{{ url("sounds/error.wav") }}');
                audio.play();

                swal({
                    text: "Not valid Code/Booking!",
                    icon: "error",
                    button: "Rescan",
                }).then(() => {
                    resumeQRScanner();
                })
            })
        } else {
            resumeQRScanner();
        }
    };

    function showPopup(data) {
        $('#modal_info').modal('show');
        $('#result').html(data);
    }

    function resumeQRScanner() {
        html5QrCode.resume();
    }

    $('#modal_info').on('hidden.bs.modal', function (event) {
        $('.scan-again').attr('disabled', false);
        resumeQRScanner();
    });

    $(document).on('click', '.scan-again', function (e) {
        e.preventDefault();

        let selected = [];
        $("#result input[name='guests']:checked").each(function () {
            selected.push($(this).val());
        });

        if (selected.length > 0) {
            $('.scan-again').attr('disabled', true);

            const postUrl = '{{ url("bookings/check-in/accommodation") }}';
            axios.post(postUrl, { guests: selected }).then(res => {
                const audio = new Audio('{{ url("sounds/success.wav") }}');
                audio.play();

                $('#modal_info').modal('hide');
            });
        }
    })

    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            var cameraId = devices[0].id;
            html5QrCode.start({ facingMode: 'environment' }, config, qrCodeSuccessCallback);
        }
    }).catch(err => {
        // handle err
    });
</script>
@endsection
