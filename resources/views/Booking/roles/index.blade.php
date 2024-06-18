@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item active">Roles & Permissions</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h4 class="card-title">Roles & Permissions</h4>
                    <div class="header-elements">
                        @can ('add setting')
                            <a href="{{ route('tenant.roles.create') }}" title="" class="btn bg-danger">
                                <i class="far fa-plus mr-1"></i> New Role
                            </a>
                        @endcan
                    </div>
                </div>
                <table class="table table-xs table-compact">
                    <thead>
                        <tr class="bg-grey-700">
                            <th>Role</th>
                            <th>Permission</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td class="vertical-top"><a href="{{ route('tenant.roles.show', [ 'id' => $role->id ]) }}" class="list-icons-item text-danger"><b>{{ strtoupper($role->name) }}</b></a></td>
                                <td class="">
                                    <span class="text-uppercase font-size-sm">
                                        {{ $role->name == 'Super Admin' ? 'all permissions granted' : $role->permissions->pluck('name')->implode(', ') }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="list-icons">
                                        @if ($role->name != 'Super Admin')
                                            <a href="{{ route('tenant.roles.show', [ 'id' => $role->id ]) }}" class="list-icons-item text-grey"><i class="icon-pencil7"></i></a>
                                            <a href="{{ route('tenant.roles.delete', [ 'id' => $role->id ]) }}" class="list-icons-item text-danger confirm-dialog" data-text="Delete this roles?"><i class="icon-trash"></i></a>
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
