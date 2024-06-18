@extends('Booking.app') 

@section('content')

    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <span class="breadcrumb-item"><a href="{{route('tenant.camps')}}" title="" class="text-grey">Camps</a></span>
                <span class="breadcrumb-item active">{{$location->name}}</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h4 class="card-title">{{$location->name}}</h4>
                        <div class="header-elements">
                            <a href="{{route('tenant.camps')}}" title="" class="btn btn-link text-slate">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0 border-0">
                        <ul class="nav nav-tabs nav-tabs-highlight justify-content-center" id="camp-tab">
                            <li class="nav-item">
                                <a href="#general" class="nav-link" data-toggle="tab">
                                    <div>
                                        <i class="icon-cog2 d-block mb-1 mt-1"></i>
                                        General Settings
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#images" class="nav-link" data-toggle="tab">
                                    <div>
                                        <i class="icon-image2 d-block mb-1 mt-1"></i>
                                        Images
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#terms-template" class="nav-link" data-toggle="tab">
                                    <div>
                                        <i class="icon-quill4 d-block mb-1 mt-1"></i>
                                        Terms &amp; Conditions
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#payment-methods" class="nav-link" data-toggle="tab">
                                    <div>
                                        <i class="icon-coins d-block mb-1 mt-1"></i>
                                        Payment methods
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body border-0 p-0">
                        <div class="tab-content">
                            <div class="tab-pane fade" id="general">
                                @include('Booking.partials.locations.general-settings') 
                            </div>

                            <div class="tab-pane fade" id="images">
                                @include('Booking.partials.locations.images') 
                            </div>

                            <div class="tab-pane fade" id="terms-template">
                                @include('Booking.partials.locations.terms-template') 
                            </div>

                            <div class="tab-pane fade" id="payment-methods">
                                @include('Booking.partials.locations.payment-methods') 
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
    <script>
    tippy('.tippy', {
        content: 'Tooltip',
        arrow: true,
    });
    var url = document.location.toString();
    if (url.match('#')) {
        var hash = '#' + (url.split('#')[1]);
        $('#camp-tab a[href="' + hash + '"]').tab('show');
    }
    $('a.nav-link').on('click', function(e) {
        window.location.hash = $(this).attr('href');
    });
    $('.daterange-empty').daterangepicker({
        autoApply: true,
        showDropdowns: true,
        minDate: "01/01/2018",
        minYear: 2023,
        maxYear: 2030,
        autoUpdateInput: false,
        locale: {
            format: 'DD.MM.YYYY'
        }
    });
    $('.daterange-empty').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
    });

    $('.daterange-empty').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    $(document).ready(function() {
        $('textarea.frl').froalaEditor({
            charCounterCount: false,
            toolbarButtons: ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'insertLink', 'insertHR', 'insertTable', 'html'],
            tableStyles: {
                'payment-email': 'payment-email'
            },
            heightMin: 400,
            heightMax: 800
        })
    });
    $('.multiselect').multiselect({
        nonSelectedText: 'Select Days',
        includeSelectAllOption: true,
    });
    if ($('#tax').length) {
        $('#tax').on('click', function (e) {
            $('#tax-container').toggle();
        })
    }
    </script>
@endsection