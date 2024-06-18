@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item active">Class Categories</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                <h4 class="m-0 mr-auto"><i class="far fa-fw fa-screen-users mr-1"></i> Class Categories</h4>
                <a href="{{ route('tenant.classes.categories.create') }}" title="" class="btn bg-danger">
                    <i class="far fa-plus mr-1"></i> New Class Category
                </a>
            </div>

            <div class="card">
                <table class="table table-xs table-compact">
                    <thead>
                        <tr class="bg-grey-700">
                            <th width="40%">Name</th>
                            <th width="28%">Short Name</th>
                            <th class="text-center" width="5%">Classes</th>
                            <th class="text-center" width="5%">Active</th>
                            <th class="text-center" width="5%">Shop</th>
                            <th class="text-center" width="12%">Booker Only</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td valign="left">
                                    <a href="{{ route('tenant.classes.categories.show', ['id' => $category->id]) }}" class="text-danger font-weight-bold">
                                        {{ $category->name }}
                                    </a>
                                </td>
                                <td><b>{{ $category->short_name }}</b></td>
                                <td class="text-center">{{ $category->classes_count }}</td>
                                <td class="text-center">{!! $category->is_active ? '<i class="far fa-fw fa-check text-success"></i>' : '<i class="fal fa-fw fa-times text-danger"></i>' !!}</td>
                                <td class="text-center">{!! $category->is_shop ? '<i class="far fa-fw fa-check text-success"></i>' : '<i class="fal fa-fw fa-times text-danger"></i>' !!}</td>
                                <td class="text-center">{!! $category->booker_only ? '<i class="far fa-fw fa-check text-success"></i>' : '<i class="fal fa-fw fa-times text-danger"></i>' !!}</td>
                                <td class="text-right">
                                    <div class="list-icons">
                                        <a href="{{ route('tenant.classes.categories.show', [ 'id' => $category->id]) }}" class="list-icons-item text-grey"><i class="icon-pencil7"></i></a>
                                        @if ($category->classes_count <= 0)
                                            <a href="{{ route('tenant.classes.categories.destroy', [ 'id' => $category->id]) }}" class="list-icons-item text-danger confirm-dialog" data-text="Delete this category?"><i class="icon-trash"></i></a>
                                        @else
                                            <a href="#" class="list-icons-item text-muted confirm-dialog tippy" data-tippy-content="Category cannot be deleted because it contains one or more classes"><i class="icon-trash"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-left">No category found</td>
                            </tr>
                        @endforelse
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
    });
</script>
@endsection