@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item"><a href="{{ route('tenant.classes.categories.index') }}" title="" class="text-grey">Class Categories</a></span>
            <span class="breadcrumb-item active">New category</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h4 class="card-title">New category</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.classes.categories.index') }}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <div class="tab-content">
                        <div class="tab-pane active">

                            <form action="{{ route('tenant.classes.categories.store') }}" method="POST">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4"><h6>Details</h6></div>
                                        <div class="col-sm-8">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Name</label>
                                                        <input type="text" name="name" placeholder="Name" class="form-control @error('name') is-invalid @enderror" maxlength="255" required value="{{ old('name') }}">
                                                        @error('name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Short Name</label>
                                                        <input type="text" name="short_name" placeholder="Short Name" class="form-control" maxlength="255" value="{{ old('short_name') }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Description</label>
                                                        <textarea name="description" class="frl form-control" placeholder="Enter description"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="form-active">
                                                            <label class="custom-control-label" for="form-active">Active ?</label>
                                                        </div>
                                                        <div class="font-size-sm text-muted">
                                                            Inactive category are not displayed in booking process
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="is_shop" value="1" class="custom-control-input" id="form-is_shop">
                                                            <label class="custom-control-label" for="form-is_shop">Show in shop</label>
                                                        </div>
                                                        <div class="font-size-sm text-muted">
                                                            Check this to show this category in shop
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="booker_only" value="1" class="custom-control-input" id="form-booker_only">
                                                            <label class="custom-control-label" for="form-booker_only">Booker Only</label>
                                                        </div>
                                                        <div class="font-size-sm text-muted">
                                                            When enabled, guest will only need to fill the booker details.
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