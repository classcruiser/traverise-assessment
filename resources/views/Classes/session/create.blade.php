@extends('Booking.app')

@section('content')

<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item"><a href="{{ route('tenant.classes.sessions.index') }}" title="" class="text-grey">Class Sessions</a></span>
            <span class="breadcrumb-item active">New Class Session</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h4 class="card-title">New Class Session</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.classes.sessions.index') }}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <div class="tab-content">
                        <div class="tab-pane active">

                            <form action="{{ route('tenant.classes.sessions.store') }}" method="POST">
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
                                                        <label>* Color</label>
                                                        <input data-jscolor="{ preset: 'dark', closeButton: true, closeText: 'OK' }" value="{{ old('color') }}" name="color" required class="form-control @error('color') is-invalid @enderror" />
                                                        @error('color')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Category</label>
                                                        <select class="form-control select-no-search" data-fouc data-placeholder="Category" name="class_category_id" required>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6"></div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="form-active" @checked(old('is_active'))>
                                                            <label class="custom-control-label" for="form-active">Active ?</label>
                                                        </div>
                                                        <div class="font-size-sm text-muted">
                                                            Inactive class session are not displayed in booking process
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4"><h6>Properties</h6></div>
                                        <div class="col-sm-8">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Instructor</label>
                                                        <select class="form-control select-no-search" data-fouc name="instructor_id">
                                                            <option value="">-- Please Select --</option>
                                                            @foreach ($instructors as $key => $value)
                                                                <option value="{{ $key }}" @selected(old('instructor_id') == $key)>{{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Max. Guest</label>
                                                        <input type="number" min="1" max="20" name="max_pax" placeholder="Maximum guest to book this class (1 to 20)" class="form-control @error('max_pax') is-invalid @enderror" value="{{ old('max_pax') }}" required>
                                                        @error('max_pax')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4"><h6>Base Pricing</h6></div>
                                        <div class="col-sm-8">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Default price</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend">
                                                                <span class="input-group-text">&euro;</span>
                                                            </span>
                                                            <input type="text" name="price" class="form-control @error('price') is-invalid @enderror" required value="{{ old('price') }}" />
                                                            @error('price')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
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
