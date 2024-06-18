@extends('Booking.app')

@section('content')

<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item"><a href="{{ route('tenant.roles') }}" title="" class="text-grey">Roles & Permissions</a></span>
            <span class="breadcrumb-item active">Edit Role</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h4 class="card-title">Edit Role: {{ $role->name }}</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.roles') }}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>
                <form action="{{ route('tenant.roles.update', [ 'id' => $role->id ]) }}" method="post">
                    <div class="card-body border-top-1 border-alpha-grey pt-3">
                        @include('Booking.partials.form-messages')
                        @include('Booking.partials.form-error')
                        <div class="row">
                            <div class="col-sm-4"><h6>Details</h6></div>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>* Role Name</label>
                                            <input type="text" name="name" placeholder="Name" class="form-control" required value="{{ $role->name }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><h6>Permissions</h6></div>
                            <div class="col-sm-8">
                                <div class="d-flex justify-content-between mb-2">
                                    <label>Select the permission you want to assign to this role</label>
                                    <a href="#" title="" class="text-danger toggle text-uppercase font-size-sm">Toggle all permissions</a>
                                </div>

                                <div class="border-1 border-alpha-grey">
                                    @foreach ($permissions as $permission)
                                        <div class=" py-2 px-3 border-bottom-1 border-alpha-grey">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input checkbox-permission checkbox-{{ $permission->id }}" id="permission-{{ $permission->id }}" name="permissions[{{ $permission->name }}]" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                <label class="custom-control-label font-size-sm text-uppercase" for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    @can ('save setting')
                        <div class="card-body">
                            <div class="text-right">
                                @csrf
                                <button class="btn bg-danger" type="submit">Update Role</button>
                            </div>
                        </div>
                    @endcan
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
    });
    $(document).ready(function() {
        $('.toggle').on('click', function (e) {
            e.preventDefault();
            const val = $('.checkbox-permission').attr('checked');
            $('.checkbox-permission').attr('checked', ! val);
        })
    });
</script>
@endsection
