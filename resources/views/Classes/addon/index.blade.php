@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item active">Class Addons</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                <h4 class="m-0 mr-auto"><i class="far fa-fw fa-layer-plus mr-1"></i> Class Addons</h4>
                <a href="{{ route('tenant.classes.addons.create') }}" title="" class="btn bg-danger">
                    <i class="far fa-plus mr-1"></i> New Class Addon
                </a>
            </div>

            <div class="card">
                <table class="table table-xs table-compact sortable" data-url="{{ route('tenant.classes.addons.sort') }}">
                    <thead>
                        <tr class="bg-grey-700">
                            <th></th>
                            <th class="">Name</th>
                            <th>Rate</th>
                            <th class="">Base price</th>
                            <th class="text-center">Default</th>
                            <th class="text-center">Active</th>
                            <th class="text-center">Sort</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($addons as $addon)
                            <tr class="bg-white" data-id="{{ $addon->id }}">
                                <td class="text-center"><span class="handler cursor-move"><i class="fal fa-bars fa-fw"></i></span></td>
                                <td class="vertical-top"><a href="{{ route('tenant.classes.addons.show', ['id' => $addon->id]) }}" class="list-icons-item text-danger"><b>{{ $addon->name }}</b></a></td>
                                <td>{!! $addon->rate_type !!}</td>
                                <td>&euro;{{ $addon->base_price }}</td>
                                <td class="text-center">
                                    <i class="far fa-fa fa-{{ $addon->add_default ? 'check text-success' : 'times text-danger' }}"></i>
                                </td>
                                <td class="text-center">
                                    <i class="far fa-fa fa-{{ $addon->is_active ? 'check text-success' : 'times text-danger' }}"></i>
                                </td>
                                <td class="text-center">{{ $addon->sort }}</td>
                                <td class="text-right">
                                    <div class="list-icons">
                                        <a href="{{ route('tenant.classes.addons.edit', ['id' => $addon->id]) }}" class="list-icons-item text-grey"><i class="icon-pencil7"></i></a>
                                        <a href="{{ route('tenant.classes.addons.destroy', ['id' => $addon->id]) }}" class="list-icons-item text-danger confirm-dialog" data-text="Delete this addon?"><i class="icon-trash"></i></a>
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
