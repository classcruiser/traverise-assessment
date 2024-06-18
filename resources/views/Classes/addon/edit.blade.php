@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <a href="{{ route('tenant.classes.addons.index') }}" title="" class="breadcrumb-item">Class Addons</a>
            <span class="breadcrumb-item active">{{ $record->name }}</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            @if (session()->has('message'))
                <div class="alert bg-green-400 text-white alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <i class="fa fa-check-circle mr-1"></i> {{session('message')}}
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h4 class="card-title">{{ $record->name }}</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.classes.addons.index') }}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>

                <form action="{{ route('tenant.classes.addons.update', ['id' => $record->id]) }}" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><h6>Details</h6></div>
                            <div class="col-sm-8">

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Name</label>
                                            <input type="text" name="name" placeholder="Name" class="form-control @error('name') is-invalid @enderror" maxlength="255" value="{{ old('name', $record->name) }}" required>
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
                                                <option value="Day" @selected(old('rate_type', $record->rate_type) === 'Day')>Day</option>
                                                <option value="Fixed" @selected(old('rate_type', $record->rate_type) === 'Fixed')>Fixed</option>
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
                                                <input type="text" name="base_price" class="form-control form-control-sm @error('base_price') is-invalid @enderror" placeholder="0.0" value="{{ old('base_price', $record->base_price) }}" required />
                                                @error('base_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Unit name</label>
                                            <input type="text" name="unit_name" placeholder="Guest, Car etc" class="form-control @error('unit_name') is-invalid @enderror" value="{{ old('unit_name', $record->unit_name) }}" required>
                                            @error('unit_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control" rows="5" placeholder="Addon description">{{ old('description', $record->description) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Min. Unit</label>
                                            <input type="number" name="min_unit" class="form-control form-control-sm @error('min_unit') is-invalid @enderror" placeholder="1" value="{{ old('min_unit', $record->min_unit) }}" />
                                            @error('min_unit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Max. Unit</label>
                                            <input type="number" name="max_unit" class="form-control form-control-sm @error('max_unit') is-invalid @enderror" placeholder="Leave blank for no limit" value="{{ old('max_unit', $record->max_unit) }}" />
                                            @error('max_unit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Sort</label>
                                            <input type="number" name="sort" class="form-control form-control-sm @error('sort') is-invalid @enderror" placeholder="1" value="{{ old('sort', $record->sort) }}" />
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
                                                <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="form-active" @checked(old('is_active', $record->is_active))>
                                                <label class="custom-control-label" for="form-active">Active</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="admin_only" value="1" class="custom-control-input" id="form-admin" @checked(old('admin_only', $record->admin_only))>
                                                <label class="custom-control-label" for="form-admin">Only visible in backend</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="add_default" value="1" class="custom-control-input" id="form-add_default" @checked(old('add_default', $record->add_default))>
                                                <label class="custom-control-label" for="form-add_default">Added by default</label>
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
                                <h6>Session associations</h6>
                            </div>
                            <div class="col-sm-8">
                                <div class="row">

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Session</label>

                                            <div class="border-1 border-alpha-grey">
                                                @foreach($categories as $category)
                                                    <div class="py-2 px-3 alpha-grey {{$loop->last ? '' : 'border-bottom-1 border-alpha-grey'}}">
                                                        <a href="javascript:" onClick="$('.checkbox-{{$category->id}}').attr('checked', ! $('.checkbox-{{$category->id}}').attr('checked'))" class="text-danger"><i class="fa fa-fw fa-home mr-1"></i> <b>{{$category->name}}</b></a>
                                                    </div>
                                                    @foreach($category->classes as $session)
                                                        <div class=" py-2 px-3 border-bottom-1 border-alpha-grey">
                                                            <div class="custom-control custom-checkbox">
                                                                <input
                                                                    type="checkbox"
                                                                    class="custom-control-input checkbox-{{$category->id}}"
                                                                    id="session-{{$session->id}}"
                                                                    name="session[{{$session->id}}]"
                                                                    {{$record->classes->contains('class_session_id', $session->id) ? 'checked' : ''}}
                                                                >
                                                                <label class="custom-control-label" for="session-{{$session->id}}">{{$session->name}}</label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-body">
                        <h4>Addon picture</h4>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-4">Select picture</label>
                            <div class="col-sm-8">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input @error('picture') is-invalid @enderror" name="picture">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                    @error('picture')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <p class="@error('picture') mt-2 @enderror"><b>CURRENT PICTURE</b></p>
                                @if (! $picture)
                                    <em>No picture uploaded yet</em>
                                @else
                                    <img src="{{$picture}}?{{time()}}" alt="" class="d-block" />
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="text-right">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="old_sessions" value="{{json_encode($record->classes)}}" />
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
