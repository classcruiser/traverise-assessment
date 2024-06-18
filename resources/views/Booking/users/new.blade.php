@extends('Booking.app')

@section('content')

<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item"><a href="{{ route('tenant.users') }}" title="" class="text-grey">Users</a></span>
            <span class="breadcrumb-item active">New User</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h4 class="card-title">New User</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.users') }}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>
                <form action="{{ route('tenant.users.insert') }}" method="post">
                    <div class="card-body border-top-1 border-alpha-grey pt-3">
                        @include('Booking.partials.form-error')
                        @include('Booking.partials.form-messages')
                        <div class="row">
                            <div class="col-sm-4"><h6>Details</h6></div>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Name</label>
                                            <input type="text" name="name" placeholder="Name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Email</label>
                                            <input type="text" name="email" placeholder="Email" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" name="password" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Active</label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="is_active" class="custom-control-input" id="form-active">
                                                <label class="custom-control-label" for="form-active">Is user active?</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><h6>Role & Permissions</h6></div>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>* Role</label>
                                            <select class="form-control select-no-search select_user_type" data-fouc data-placeholder="Role" name="role">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>* Camp Permission</label>
                                            <div class="border-1 border-alpha-grey">
                                                @foreach ($locations as $location)
                                                    <div class="py-1">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" id="location-{{ $location->id }}" class="custom-control-input checkbox-{{ $location->id }}" name="allowed_camps[{{ $location->id }}]">
                                                            <label class="custom-control-label" for="location-{{ $location->id }}">{{ $location->name }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Allow drag and drop in Calendar</label>
                                            <div class="py-1">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" id="dnd" class="custom-control-input" name="can_drag">
                                                    <label class="custom-control-label" for="dnd">Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @can ('add user')
                        <div class="card-body">
                            <div class="text-right">
                                @csrf
                                <button class="btn bg-danger" type="submit">Create user</button>
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
</script>
@endsection
