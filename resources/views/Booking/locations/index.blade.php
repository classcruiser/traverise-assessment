@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item active">Camps</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-home mr-1"></i> Camps</h4>
                @can ('add camp')
                <a href="{{ route('tenant.camps.create') }}" title="" class="btn bg-danger">
                    <i class="far fa-plus mr-1"></i> New Camp
                </a>
                @endcan
            </div>
            <div class="card">
                <table class="table table-xs table-compact">
                    <thead>
                        <tr class="bg-grey-700">
                            <th>Name</th>
                            <th class="text-center">Active</th>
                            <th class="text-center">Abbr</th>
                            <th class="text-right">Room count</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($locations as $index => $camp)
                        <?php $total_rooms = count($camp->rooms); ?>
                        <tr>
                            <td><a href="{{ route('tenant.camps.show', [ 'id' => $camp->id ]) }}#general" class="text-danger"><b>{{ $camp->name }}</b></a></td>
                            <td class="text-center">{!! $camp->active ? '<i class="far fa-fw fa-check text-success"></i>' : '<i class="fal fa-fw fa-times text-danger"></i>' !!}</td>
                            <td class="text-center">{{ $camp->abbr }}</td>
                            <td class="text-right">{{ $total_rooms }} {{ \Str::plural('Room', $total_rooms) }}</td>
                            <td class="text-right">
                                <div class="list-icons">
                                    <a href="{{ route('tenant.camps.duplicate', ['id' => $camp->id]) }}" title="" class="list-icons-item text-primary tippy confirm-dialog" data-text="This will create a duplicate including all the details. Do you want to proceed?" data-tippy-content="Duplicate camp with all the details"><i class="icon-copy3"></i></a>
                                    <a href="{{ route('tenant.camps.show', ['id' => $camp->id]) }}#camp-details" class="list-icons-item text-secondary"><i class="icon-pencil7"></i></a>
                                    @if (!$loop->first)
                                        <a href="{{ route('tenant.camps.destroy', ['id' => $camp->id]) }}" class="list-icons-item text-danger confirm-dialog" data-text="Delete this camp? This action is irreversible!"><i class="icon-bin"></i></a>
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
