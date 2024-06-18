@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item"><a href="{{ route('tenant.classes.multi-pass.index') }}" title="" class="text-grey">Class Multi Pass</a></span>
            <span class="breadcrumb-item active">New pass</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h4 class="card-title">New pass</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.classes.multi-pass.index') }}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <div class="tab-content">
                        <div class="tab-pane active">

                            <form action="{{ route('tenant.classes.multi-pass.update', ['id' => $pass->id]) }}" method="POST">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4"><h6>Details</h6></div>
                                        <div class="col-sm-8">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Name</label>
                                                        <input type="text" name="name" placeholder="Name" class="form-control @error('name') is-invalid @enderror" maxlength="255" required value="{{ old('name', $pass->name) }}">
                                                        @error('name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Type</label>
                                                        <select class="form-control select-no-search" data-fouc data-placeholder="Pass type" name="type" required>
                                                            @foreach ($types as $type)
                                                                <option value="{{ $type }}" {{ old('type', $pass->type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Amount Type</label>
                                                        <select class="form-control select-no-search" data-fouc data-placeholder="Amount type" name="amount_type" required>
                                                            <option value="VALUE" {{ $pass->amount_type == 'VALUE' ? 'selected' : '' }}>VALUE</option>
                                                            <option value="PERCENTAGE" {{ $pass->amount_type == 'PERCENTAGE' ? 'selected' : '' }}>PERCENTAGE</option>
                                                        </select>
                                                        @error('amount_type')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <span class="d-block mt-1 font-italic">Only if you select "Voucher" type</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Amount</label>
                                                        <input type="text" name="amount" placeholder="Amount" class="form-control @error('amount') is-invalid @enderror" maxlength="255" required value="{{ old('amount', $pass->amount) }}">
                                                        @error('amount')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Price</label>
                                                        <input type="text" name="price" placeholder="Price" class="form-control @error('price') is-invalid @enderror" maxlength="255" required value="{{ old('price', $pass->price) }}">
                                                        @error('price')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Pass Code</label>
                                                        <div class="input-group">
                                                            <input type="text" id="pass-code" name="code" placeholder="Pass Code" class="form-control @error('code') is-invalid @enderror" maxlength="255" value="{{ old('code', $pass->code) }}" aria-label="Pass Code" aria-describedby="btn-generate-passcode">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary tippy" type="button" id="btn-generate-passcode" data-tippy-content="Generate Random Code"><i class="icon-spinner9"></i></button>
                                                            </div>
                                                        </div>
                                                        <span class="d-block mt-1 font-italic">Only if you select "Voucher" type</span>
                                                        @if ($pass->type === "VOUCHER" && $pass->code)
                                                            <span class="d-block font-italic">Generated at: {{ $pass->code_generated_at?->format('d.m.Y') ?? $pass->created_at->format('d.m.Y') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-sm-6"></div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Description</label>
                                                        <textarea name="description" class="frl form-control" placeholder="Enter description">{!! old('description', $pass->description) !!}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="form-active" @checked(old('is_active', $pass->is_active))>
                                                            <label class="custom-control-label" for="form-active">Active ?</label>
                                                        </div>
                                                        <div class="font-size-sm text-muted">
                                                            Inactive category are not displayed in booking process
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
                                                        <input type="number" min="0" name="usage_limit" class="form-control" placeholder="Enter 0 or leave blank for unlimited usage" value="{{ old('usage_limit', $pass->usage_limit) }}">
                                                        <span class="d-block mt-1 font-italic">Only if you select "Voucher" type</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Limit to</label>
                                                        <select class="form-control select-no-search" data-fouc data-placeholder="Only for" name="class_session_id">
                                                            <option value="0">No limit / All sessions</option>
                                                            @foreach ($sessions as $session)
                                                                @if ($session->category)
                                                                <option value="{{ $session->id }}" {{ old('class_session_id', $pass->class_session_id) == $session->id ? 'selected' : '' }}>{{ $session->category->name }} &raquo; {{ $session->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Pass Expiry</label>
                                                        <div class="d-flex justify-content-start">
                                                            <input type="text" min="0" name="expiry_amount" class="form-control mr-2" placeholder="1" value="{{ old('expiry_amount', $expiry_amount) }}" style="width: 50px;">
                                                            <select class="form-control select-no-search" data-fouc data-placeholder="Expiry type" name="expiry_type">
                                                                <option value="0">No expire limit</option>
                                                                @foreach ($expiry_types as $type)
                                                                    <option value="{{ $type }}" {{ old('expiry_type', $expiry_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                                                @endforeach
                                                            </select>
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
                                        @csrf
                                        @method('PUT')
                                        <button class="btn bg-danger new-room">Submit</button>
                                    </div>
                                </div>
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
@endsection
