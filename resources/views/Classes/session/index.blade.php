@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item active">Class Sessions</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                <h4 class="m-0 mr-auto"><i class="far fa-fw fa-person-chalkboard mr-1"></i> Class Sessions</h4>
                <a href="{{ route('tenant.classes.sessions.create') }}" title="" class="btn bg-danger">
                    <i class="far fa-plus mr-1"></i> New Class Session
                </a>
            </div>

            <div class="card">
                <table class="table table-xs table-compact">
                    <thead>
                        <tr class="bg-grey-700">
                            <th>Name</th>
                            <th>Default price</th>
                            <th>Instructor</th>
                            <th>Max Pax</th>
                            <th>Active</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td colspan="6" class="bg-grey-100 text-uppercase font-weight-bold p-0">
                                    <a href="#" class="toggle-row text-body d-block border-bottom px-2 py-2" data-target="row-{{ $category->id }}" title="">
                                        <i id="row-{{ $category->id }}" class="fa fa-angle-down mr-1 pl-1"></i> {{ $category->name }}
                                    </a>
                                </td>
                            </tr>
                            @forelse ($category->classes as $classes)
                                <tr class="row-{{ $category->id }}" data-id="{{ $classes->id }}">
                                    <td valign="center">
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="circle" style="background-color: @if ($classes->color) #{{ $classes->color }} @else #FFFFFF @endif">&nbsp;</div>
                                            <div class="font-weight-bold">
                                                <a href="{{ route('tenant.classes.sessions.show', ['id' => $classes->id]) }}" class="text-danger">
                                                    {{ $classes->name }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td><b>&euro;{{ $classes->price }}</b></td>
                                    <td>{{ $classes->instructor?->name ?? '--' }}</td>
                                    <td>{{ $classes->max_pax }} {{ Str::plural('Pax', $classes->max_pax) }}</td>
                                    <td class="pl-4">{!! $classes->is_active ? '<i class="far fa-fw fa-check text-success"></i>' : '<i class="fal fa-fw fa-times text-danger"></i>' !!}</td>
                                    <td class="text-right">
                                        <div class="list-icons">
                                            <a href="{{ route('tenant.classes.sessions.edit', [ 'id' => $classes->id]) }}" class="list-icons-item text-grey"><i class="icon-pencil7"></i></a>
                                            <a href="{{ route('tenant.classes.sessions.destroy', [ 'id' => $classes->id]) }}" class="list-icons-item text-danger confirm-dialog" data-text="WARNING: Deleting this session will also delete all schedules and booking connected to this session and it cannot be undone. Are you sure you want to proceed?"><i class="icon-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-left">No classes found</td>
                                </tr>
                            @endforelse
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
    $('.toggle-row').on('click', function (e) {
        e.preventDefault();
        const target = $(this).data('target');

        $('.' + target).toggle();
        if ($('#' + target).hasClass('fa-angle-right')) {
            $('#' + target).removeClass('fa-angle-right').addClass('fa-angle-down');
        } else {
            $('#' + target).removeClass('fa-angle-down').addClass('fa-angle-right');
        }
    });
</script>
@endsection
