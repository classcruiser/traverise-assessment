@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item active">Guests</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper">
        <div class="content">
            @include('Booking.partials.form-messages')
            <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                <h4 class="m-0 mr-auto d-none d-md-block"><i class="fal fa-fw fa-user-alt mr-1"></i> All Guests</h4>
                <button class="btn btn-labeled btn-labeled-left bg-orange-400 ml-1 collapsed" data-toggle="collapse" href="#advanced-search">
                    <b><i class="icon-search4"></i></b> Advanced Search
                </button>
            </div>
            @include('Classes.partials.guest.advanced-search')

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-xs table-compact dark">
                        <thead>
                            <tr class="bg-grey-700">
                                <th class="two wide">Name</th>
                                <th>Client ID</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Country</th>
                                <th class="text-center">Bookings</th>
                                <th class="text-center">Multi Pass</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $record)
                            <tr>
                                <td>
                                    <a href="{{ route('tenant.classes.guests.show', ['id' => $record->id]) }}" class="text-danger"><b>{{ $record->full_name }}</b></a>
                                </td>
                                <td class="text-info">{{ $record->client_number ?? '--' }}</td>
                                <td>{{ $record->email }}</td>
                                <td>{{ $record->phone ?? '--' }}</td>
                                <td>{{ strtoupper($record->country) }}</td>
                                <td class="text-center">{{ $record->classes_count }}</td>
                                <td class="text-center">{{ $record->passes_count }}</td>
                                <td class="text-right">
                                    <div class="list-icons">
                                        <a href="{{ route('tenant.classes.guests.edit', [ 'guest' => $record]) }}" class="list-icons-item text-grey"><i class="icon-pencil7"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-md-flex justify-content-between align-items-center">
                <div>{{ $records->appends($_GET)->links() }}</div>
                @can('export bookings')
                    <a href="{{(request()->fullUrl()) . (request()->has('_token') ? '&' : '?')}}export=true" title="" class="btn btn-success d-block d-md-inline-block mt-2">
                        <i class="fa fa-fw fa-file-excel"></i> Export to Excel
                    </a>
                @endif
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
