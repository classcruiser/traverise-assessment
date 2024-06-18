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
            <span class="breadcrumb-item active">Edit tax</span>
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

            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h4 class="card-title">Edit Tax</h4>
                    <div class="header-elements">
                        <a href="{{route('tenant.taxes')}}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>

                <form action="{{ route('tenant.taxes.update', ['id' => $tax->id]) }}" method="post" id="edit-tax" enctype="multipart/form-data">
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
                                            <input type="text" name="name" placeholder="Name" class="form-control" value="{{ $tax->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Type</label>
                                            <select class="form-control select-no-search" data-fouc data-placeholder="Rate type" name="type" required id="tax_type">
                                                @foreach (\App\Enums\Tax::taxTypes() as $type)
                                                    <option value="{{ $type }}" {{ strtolower($tax->type) == strtolower($type->value) ? 'selected' : '' }}>{{ ucfirst($type->readable()) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Rate</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-prepend" id="is-flat" {!! $tax->type != 'flat' ? 'style="display: none"' : '' !!}>
                                                    <span class="input-group-text"><i class="fa fa-fw fa-euro-sign"></i></span>
                                                </span>
                                                <input type="text" name="rate" class="form-control form-control-sm" placeholder="0.0" required value="{{ $tax->rate }}" />
                                                <span class="input-group-append" id="is-percentage" {!! $tax->type != 'percentage' ? 'style="display: none"' : '' !!}>
                                                    <span class="input-group-text"><i class="fa fa-fw fa-percent"></i></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="flat-options" {!! $tax->type != 'flat' ? 'style="display: none"' : '' !!}>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Calculation Type</label>
                                            <select class="form-control select-no-search" data-fouc data-placeholder="Calculation type" name="calculation_type">
                                                @foreach (Tax::calculationTypes() as $type)
                                                    <option value="{{ $type }}" {{ strtolower($tax->calculation_type) == strtolower($type->value) ? 'selected' : '' }}>{{ ucfirst($type->readable()) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Calculation Charge</label>
                                            <select class="form-control select-no-search" data-fouc data-placeholder="Calculation charge" name="calculation_charge">
                                                @foreach (Tax::calculationCharges() as $charge)
                                                    <option value="{{ $charge }}" {{ strtolower($tax->calculation_charge) == strtolower($charge->value) ? 'selected' : '' }}>{{ ucfirst($charge->readable()) }}</option>
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
                                                    <option value="{{ $options }}" {{ strtolower($tax->tax_type) == strtolower($options->value) ? 'selected' : '' }}>{{ ucfirst($options->readable()) }}</option>
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
                                                <input type="checkbox" name="is_active" class="custom-control-input" id="form-active" {{ $tax->is_active ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="form-active">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <h6>Tax Setting</h6>
                            </div>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <p><b>Location</b></p>
                                        @forelse($locations as $location)
                                            <div class="custom-control custom-checkbox">
                                                <input
                                                    type="checkbox"
                                                    name="tax_location[{{ $location->id }}]"
                                                    class="custom-control-input checkbox-{{ $location->id }}"
                                                    id="location-{{ $location->id }}"
                                                    {{ $tax->accommodations()->contains('model_id', $location->id) ? 'checked' : '' }}
                                                    {{ isset($settings['App\Models\Booking\Location']) && !$tax->accommodations()->contains('model_id', $location->id) && (collect($settings['App\Models\Booking\Location'])->contains($location->id)) ? 'disabled' : '' }}
                                                >
                                                <label class="custom-control-label" for="location-{{ $location->id }}">
                                                    {{ $location->name }}
                                                </label>
                                            </div>
                                            <p class="ml-2"><b>Accommodations</b></p>
                                            @foreach ($location->rooms as $room)
                                                <div class="custom-control custom-checkbox ml-2">
                                                    <input
                                                        type="checkbox"
                                                        name="tax_accommodations[{{ $room->id }}]"
                                                        class="custom-control-input checkbox-{{ $room->id }}"
                                                        id="room-{{ $room->id }}"
                                                        {{ $tax->rooms()->contains('model_id', $room->id) ? 'checked' : '' }}
                                                        {{ isset($settings['App\Models\Booking\Room']) && !$tax->rooms()->contains('model_id', $room->id) && (collect($settings['App\Models\Booking\Room'])->contains($room->id)) ? 'disabled' : '' }}
                                                    >
                                                    <label class="custom-control-label" for="room-{{ $room->id }}">
                                                        {{ $room->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @empty
                                            <div class="mt-2">
                                                <em>You don't have a location yet. Please <a href="{{ route('tenant.camps') }}" title="">click here</a> to add new addon.</em>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 mt-4">
                                        <p><b>Add ons</b></p>
                                        @forelse($addons as $addon)
                                            <div class="custom-control custom-checkbox">
                                                <input
                                                    type="checkbox"
                                                    name="tax_addons[{{ $addon->id }}]"
                                                    class="custom-control-input checkbox-{{ $addon->id }}"
                                                    id="addon-{{ $addon->id }}"
                                                    {{ $tax->addons()->contains('model_id', $addon->id) ? 'checked' : '' }}
                                                    {{ isset($settings['App\Models\Booking\Extra']) && !$tax->addons()->contains('model_id', $addon->id) && (collect($settings['App\Models\Booking\Extra'])->contains($addon->id)) ? 'disabled' : '' }}
                                                >
                                                <label class="custom-control-label" for="addon-{{ $addon->id }}">
                                                    {{ $addon->name }}
                                                </label>
                                            </div>
                                        @empty
                                            <div class="mt-2">
                                                <em>You don't have add-on yet. Please <a href="{{ route('tenant.addons') }}" title="">click here</a> to add new add on.</em>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 mt-4">
                                        <p><b>Transfer Add ons</b></p>
                                        @forelse($transfers as $transfer)
                                            <div class="custom-control custom-checkbox">
                                                <input
                                                    type="checkbox"
                                                    name="tax_transfers[{{ $transfer->id }}]"
                                                    class="custom-control-input checkbox-{{ $transfer->id }}"
                                                    id="transfer-{{ $transfer->id }}"
                                                    {{ $tax->transfers()->contains('model_id', $transfer->id) ? 'checked' : '' }}
                                                    {{ isset($settings['App\Models\Booking\TransferExtra']) && !$tax->transfers()->contains('model_id', $transfer->id) && (collect($settings['App\Models\Booking\TransferExtra'])->contains($transfer->id)) ? 'disabled' : '' }}
                                                >
                                                <label class="custom-control-label" for="transfer-{{ $transfer->id }}">
                                                    {{ $transfer->name }}
                                                </label>
                                            </div>
                                        @empty
                                            <div class="mt-2">
                                                <em>You don't have transfer add-on yet. Please <a href="{{ route('tenant.transfers') }}" title="">click here</a> to add new transfer add on.</em>
                                            </div>
                                        @endforelse
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
