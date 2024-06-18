@php
    use App\Enums\Tax;
@endphp
@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <a href="{{route('tenant.taxes')}}" title="" class="breadcrumb-item">Tax</a>
            <span class="breadcrumb-item active">new tax</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            @if(session()->has('messages'))
                <div class="alert bg-green-400 text-white alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <i class="fa fa-check-circle mr-1"></i> {{session('messages')}}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert bg-danger text-white alert-dismissible">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                <i class="fa fa-exclamation-triangle mr-1"></i> {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h4 class="card-title">New Tax</h4>
                    <div class="header-elements">
                        <a href="{{route('tenant.taxes')}}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>

                <form action="{{ route('tenant.taxes.insert') }}" method="post" id="edit-tax" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <h6>Details</h6>
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
                                            <label>* Type</label>
                                            <select class="form-control select-no-search" data-fouc data-placeholder="Rate type" name="type" required id="tax_type">
                                                @foreach (\App\Enums\Tax::taxTypes() as $type)
                                                    <option value="{{ $type }}">{{ ucfirst($type->readable()) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Rate</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-prepend" id="is-flat">
                                                    <span class="input-group-text"><i class="fa fa-fw fa-euro-sign"></i></span>
                                                </span>
                                                <input type="text" name="rate" class="form-control form-control-sm" placeholder="0.0" required />
                                                <span class="input-group-append" id="is-percentage" style="display: none;">
                                                    <span class="input-group-text"><i class="fa fa-fw fa-percent"></i></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="flat-options">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Calculation Type</label>
                                            <select class="form-control select-no-search" data-fouc data-placeholder="Calculation type" name="calculation_type">
                                                @foreach (Tax::calculationTypes() as $type)
                                                    <option value="{{ $type }}">{{ ucfirst($type->readable()) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Calculation Charge</label>
                                            <select class="form-control select-no-search" data-fouc data-placeholder="Calculation charge" name="calculation_charge">
                                                @foreach (Tax::calculationCharges() as $charge)
                                                    <option value="{{ $charge }}">{{ ucfirst($charge->readable()) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>
                                                Inclusion option
                                                <span>
                                                    <i class="far fa-question-circle tippy" data-tippy-content="Inclusion option is used to determine if the tax is included in the price or not."></i>
                                                </span>
                                            </label>
                                            <select class="form-control select-no-search" data-fouc data-placeholder="Inclusion option" name="tax_type">
                                                @foreach (Tax::taxInclusionOptions() as $options)
                                                    <option value="{{ $options }}">{{ ucfirst($options->readable()) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6"></div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="is_active" class="custom-control-input" id="form-active">
                                                <label class="custom-control-label" for="form-active">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    @can('manage taxes')
                        <div class="card-body">
                            <div class="text-right">
                                @csrf
                                <button class="btn bg-danger">Submit</button>
                            </div>
                        </div>
                    @endcan
                    <!-- end card body -->

                </form>

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
    })
    $('#tax_type').on('change', function (e) {
        const val = e.target.value;
        if (val === 'flat') {
            $('#is-flat').show();
            $('#is-percentage').hide();
            $('#flat-options').show();
        } else {
            $('#is-flat').hide();
            $('#is-percentage').show();
            $('#flat-options').hide();
        }
    })
</script>
@endsection
