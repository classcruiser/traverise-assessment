@extends('Booking.app') 

@section('content')

    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <span class="breadcrumb-item"><a href="{{route('tenant.camps')}}" title="" class="text-grey">Camps</a></span>
                <span class="breadcrumb-item active">New Camp</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h4 class="card-title">New Camp</h4>
                        <div class="header-elements">
                            <a href="{{route('tenant.camps')}}" title="" class="btn btn-link text-slate">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>
                    <div class="card-body border-0 p-0">
                        <div class="tab-content">
                            <div class="tab-pane active" id="general">
                                <form action="javascript:" method="post" id="camp-details">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <h6>Camp Details</h6>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label>* Name</label>
                                                            <input type="text" name="name" placeholder="Name" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Short name</label>
                                                            <input type="text" name="short_name" placeholder="Short name" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Abbreviation</label>
                                                            <input type="text" name="abbr" placeholder="Abbreviation" class="form-control" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label>Location address</label>
                                                            <textarea name="address" placeholder="Address" class="form-control" rows="8"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Email address</label>
                                                            <input type="text" name="contact_email" placeholder="Email" class="form-control" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Phone number</label>
                                                            <input type="text" name="phone" placeholder="Phone" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body {{ tenant('plan') == 'events' ? 'd-none' : '' }}">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <h6>Advanced Settings</h6>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Private Room</label>
                                                            <select class="form-control select-no-search" data-fouc data-placeholder="Private room pricing" name="price_type">
                                                                <option value="room">Priced by Room</option>
                                                                <option value="guest">Priced by Guest</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Minimum Booking Nights</label>
                                                            <input type="number" name="minimum_nights" placeholder="1" class="form-control" value="1">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Minimum Check in date</label>
                                                            <input type="text" name="minimum_checkin" class="form-control date-range-single" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6"></div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="d-block mb-2">Third party location?</label>
                                                            <div class="custom-control custom-checkbox custom-control-inline mt-1">
                                                                <input type="checkbox" name="third_party" class="custom-control-input" id="form-third-party">
                                                                <label class="custom-control-label" for="form-third-party">Third party location</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="d-block mb-2">Allow pending booking?</label>
                                                            <div class="custom-control custom-checkbox custom-control-inline mt-1">
                                                                <input type="checkbox" name="allow_pending" class="custom-control-input" id="form-allow-pending">
                                                                <label class="custom-control-label" for="form-allow-pending">Allow pending booking</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="card-body {{ tenant('plan') == 'events' ? 'd-none' : '' }}">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <h6>Services</h6>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label>Camp Service</label>
                                                            <textarea name="service" placeholder="Service" class="form-control" rows="12"></textarea>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="card-body {{ tenant('plan') == 'events' ? 'd-none' : '' }}">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <h6>Deposit Settings</h6>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="row">

                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-checkbox custom-control-inline mt-1">
                                                                <input type="checkbox" name="enable_deposit" class="custom-control-input" id="form-deposit" data-target="#deposit-form" data-toggle="collapse">
                                                                <label class="custom-control-label" for="form-deposit">Enable?</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6"></div>
                                                    <div class="col-sm-12 collapse" id="deposit-form">
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label>Deposit type</label>
                                                                    <select class="form-control select-no-search" data-fouc data-placeholder="Deposit type" name="deposit_type">
                                                                        <option value="fixed">Price by </option>
                                                                        <option value="percent">Percent</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>Value</label>
                                                                    <input type="number" name="deposit_value" placeholder="0.0" class="form-control" />
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>Due in</label>
                                                                    <div class="input-group">
                                                                        <input type="number" name="deposit_due" class="form-control" />
                                                                        <span class="input-group-append">
                                                                            <span class="input-group-text">Days</span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="card-body {{ tenant('plan') == 'events' ? 'd-none' : '' }}">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <h6>Duration Discount</h6>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="row">

                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-checkbox custom-control-inline mt-1">
                                                                <input type="checkbox" name="duration_discount" class="custom-control-input" id="form-duration" data-target="#duration-form" data-toggle="collapse">
                                                                <label class="custom-control-label" for="form-duration">Enable?</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6"></div>
                                                    <div class="col-sm-12 collapse" id="duration-form">
                                                        <div class="row">

                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>Daily discount</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-prepend">
                                                                            <span class="input-group-text"><i class="far fa-percent"></i></span>
                                                                        </span>
                                                                        <input type="number" name="min_discount" class="form-control form-control-sm" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>Max discount</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-prepend">
                                                                            <span class="input-group-text"><i class="far fa-percent"></i></span>
                                                                        </span>
                                                                        <input type="number" name="max_discount" class="form-control form-control-sm" />
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    @can('add camp')
                                        <div class="card-body">
                                            <div class="text-right">
                                                @csrf
                                                <button class="btn bg-danger create-new-camp" data-url="{{route('tenant.camps.insert')}}">Submit</button>
                                            </div>
                                        </div>
                                    @endcan
                                    <!-- end card body -->

                                </form>
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
    </script>
@endsection