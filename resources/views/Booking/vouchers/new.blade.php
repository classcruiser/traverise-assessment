@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <a href="{{ route('tenant.vouchers') }}" title="" class="breadcrumb-item">Vouchers</a>
            <span class="breadcrumb-item active">New voucher</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            @if (session()->has('messages'))
            <div class="alert bg-green-400 text-white alert-dismissible">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <i class="fa fa-check-circle mr-1"></i> {{ session('messages') }}
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h4 class="card-title">New Voucher</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.vouchers') }}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>

                <form action="{{ route('tenant.vouchers.insert') }}" method="post" id="edit-package">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <h6>Details</h6>
                            </div>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Name</label>
                                            <input type="text" name="name" placeholder="Name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Code</label>
                                            <div class="input-group">
                                                <input type="text" id="voucher_code" name="voucher_code" placeholder="Code" required class="form-control @error('voucher_code') is-invalid @enderror" maxlength="191" value="{{ old('voucher_code') }}" aria-label="Code" aria-describedby="btn-generate-voucher-code">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary tippy" type="button" id="btn-generate-voucher-code" data-tippy-content="Generate Random Code"><i class="icon-spinner9"></i></button>
                                                </div>
                                            </div>
                                            @error('voucher_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Amount Type</label>
                                            <select class="form-control select-no-search" data-fouc data-placeholder="Amount type" name="amount_type" required>
                                                <option @selected(old('amount_type') == 'VALUE') value="VALUE">VALUE</option>
                                                <option @selected(old('amount_type') == 'PERCENTAGE') value="PERCENTAGE">PERCENTAGE</option>
                                            </select>
                                            @error('amount_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Amount</label>
                                            <input type="text" name="amount" placeholder="Amount" class="form-control @error('amount') is-invalid @enderror" required value="{{ old('amount') }}">
                                            @error('amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Description / Terms</label>
                                            <textarea name="terms" class="form-control" rows="10" placeholder="Description" required>{{ old('terms') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="form-active" @checked(old('is_active'))>
                                                <label class="custom-control-label" for="form-active">Active ?</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><h6>Restriction</h6></div>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Usage Limit</label>
                                            <input type="number" min="0" name="usage_limit" class="form-control" placeholder="Enter 0 or leave blank for unlimited usage" value="{{ old('usage_limit') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Voucher Expiry</label>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-fw fa-calendar-alt"></i></span>
                                                </span>
                                                <input type="text" class="form-control date-time-picker" name="expired_at" autocomplete="off" value="{{ old('expired_at') }}" placeholder="Expired At">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="text-right">
                            {!! csrf_field() !!}
                            <button class="btn bg-danger" type="submit">Submit</button>
                        </div>
                    </div>
                    <!-- end card body -->

                </form>

            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
    $('.date-time-picker').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        autoApply: true,
        minDate: '{{ today()->format("d/m/Y") }}',
        locale: {
            cancelLabel: 'Clear',
            format: 'DD.MM.YYYY'
        }
    });

    $('.date-time-picker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD.MM.YYYY'));
    });
</script>
@endsection
