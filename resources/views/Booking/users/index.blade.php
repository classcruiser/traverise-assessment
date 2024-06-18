@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item active">Users</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h4 class="card-title">Users</h4>
                    <div class="header-elements">
                        @can ('add user')
                            <a href="{{ route('tenant.users.create') }}" title="" class="btn bg-danger">
                                <i class="far fa-plus mr-1"></i> New User
                            </a>
                        @endcan
                    </div>
                </div>
                <table class="table table-xs table-compact">
                    <thead>
                        <tr class="bg-grey-700">
                            <th>Name</th>
                            <th>Email</th>
                            <th class="text-right">Role</th>
                            <th class="text-right">Allowed Camps</th>
                            <th class="text-center">Drag'n'drop</th>
                            <th class="text-center">Active</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="vertical-top"><a href="{{ auth()->user()->can('edit user') ? route('tenant.users.show', [ 'id' => $user->id ]) : '#' }}" class="list-icons-item text-danger"><b>{{ $user->name }}</b></a></td>
                                <td class="font-weight-bold">{{ $user->email }}</td>
                                <td class="text-right text-uppercase">{{ $user->getRoleNames()->implode(', ') }}</td>
                                <td class="text-right">
                                    @if ($user->hasRole('Super Admin'))
                                        All Camps
                                    @else
                                        {!! $user->allowedCampsFormatted($locations) !!}
                                    @endif
                                </td>
                                <td class="text-center">
                                    <i class="fa fa-{{ $user->can_drag ? 'check text-success' : 'times text-danger' }}"></i>
                                </td>
                                <td class="text-center">
                                    <i class="fa fa-{{ $user->is_active ? 'check text-success' : 'times text-danger' }}"></i>
                                </td>
                                <td class="text-right">
                                    <div class="list-icons">
                                        @can ('edit user')
                                            <a href="{{ route('tenant.users.show', [ 'id' => $user->id ]) }}" class="list-icons-item text-grey"><i class="icon-pencil7"></i></a>
                                        @endcan
                                        @if (!$user->hasRole('Super Admin'))
                                            @can ('delete user')
                                                <a href="{{ route('tenant.users.delete', [ 'id' => $user->id ]) }}" class="list-icons-item text-danger confirm-dialog" data-text="Delete this user?"><i class="icon-trash"></i></a>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

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
</script>
@endsection
