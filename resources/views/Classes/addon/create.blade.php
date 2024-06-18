@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <a href="{{ route('tenant.classes.addons.index') }}" title="" class="breadcrumb-item">Class Addons</a>
            <span class="breadcrumb-item active">Add new class addon</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h4 class="card-title">Add Class Addon</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.classes.addons.index') }}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>

                <form action="{{ route('tenant.classes.addons.store') }}" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><h6>Details</h6></div>
                            <div class="col-sm-8">

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Name</label>
                                            <input type="text" name="name" placeholder="Name" class="form-control @error('name') is-invalid @enderror" maxlength="255" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Rate type</label>
                                            <select class="form-control @error('rate_type') is-invalid @enderror select-no-search" data-fouc data-placeholder="Rate type" name="rate_type" required>
                                                <option></option>
                                                <option value="Day" @selected(old('rate_type') === 'Day')>Day</option>
                                                <option value="Fixed" @selected(old('rate_type') === 'Fixed')>Fixed</option>
                                            </select>
                                            @error('rate_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Base price</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-fw fa-euro-sign"></i></span>
                                                </span>
                                                <input type="text" name="base_price" class="form-control form-control-sm @error('base_price') is-invalid @enderror" placeholder="0.0" value="{{ old('base_price') }}" required />
                                                @error('base_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Unit name</label>
                                            <input type="text" name="unit_name" placeholder="Guest, Car etc" class="form-control @error('unit_name') is-invalid @enderror" value="{{ old('unit_name', 'person') }}" required>
                                            @error('unit_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control" rows="5" placeholder="Addon description">{{ old('description') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Min. Unit</label>
                                            <input type="number" name="min_unit" class="form-control form-control-sm @error('min_unit') is-invalid @enderror" placeholder="1" value="{{ old('min_unit') }}" />
                                            @error('min_unit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Max. Unit</label>
                                            <input type="number" name="max_unit" class="form-control form-control-sm @error('max_unit') is-invalid @enderror" placeholder="Leave blank for no limit" value="{{ old('max_unit') }}" />
                                            @error('max_unit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Sort</label>
                                            <input type="number" name="sort" class="form-control form-control-sm @error('sort') is-invalid @enderror" placeholder="1" value="{{ old('sort') }}" />
                                            @error('sort')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="form-active" @checked(old('is_active'))>
                                                <label class="custom-control-label" for="form-active">Active</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="admin_only" value="1" class="custom-control-input" id="form-admin" @checked(old('admin_only'))>
                                                <label class="custom-control-label" for="form-admin">Only visible in backend</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="add_default" value="1" class="custom-control-input" id="form-add_default" @checked(old('add_default'))>
                                                <label class="custom-control-label" for="form-add_default">Added by default</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="text-right">
                            @csrf
                            <button class="btn bg-danger">Submit</button>
                        </div>
                    </div>
                    <!-- end card body -->

                </form>

            </div>
        </div>

    </div>
</div>
@endsection
