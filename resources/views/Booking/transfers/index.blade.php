@extends('Booking.app') 

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <span class="breadcrumb-item active">Transfers</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h4 class="card-title">Transfers</h4>
                        <div class="header-elements">
                            @can('add setting')
                                <a href="{{route('tenant.transfers.create')}}" title="" class="btn bg-danger">
                                    <i class="far fa-plus mr-1"></i> New Transfer
                                </a>
                            @endcan
                        </div>
                    </div>
                    <table class="table table-xs table-compact">
                        <thead>
                            <tr class="bg-grey-700">
                                <th class="">Name</th>
                                <th class="">Direction</th>
                                <th>Location</th>
                                <th class="text-center">Complimentary</th>
                                <th class="text-center">Default</th>
                                <th class="text-center">Active</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transfers as $transfer)
                                <tr>
                                    <td class="vertical-top"><a href="{{route('tenant.transfers.show', [ 'id' => $transfer->id ])}}" class="list-icons-item text-danger"><b>{{$transfer->name}}</b></a></td>
                                    <td>{{$transfer->direction}}</td>
                                    <td>{!! $transfer['location_details'] !!}</td>
                                    <td class="text-center">
                                        <i class="far fa-fa fa-{{$transfer->is_complimentary ? 'check text-success' : 'times text-danger'}}"></i>
                                        {{$transfer->is_complimentary ? '(min '. $transfer->complimentary_min_nights .' nights)' : ''}}
                                    </td>
                                    <td class="text-center">
                                        <i class="far fa-fa fa-{{$transfer->add_default ? 'check text-success' : 'times text-danger'}}"></i>
                                        {{$transfer->add_default ? '(min '. $transfer->default_min_nights .' nights)' : ''}}
                                    </td>
                                    <td class="text-center">
                                        <i class="far fa-fa fa-{{$transfer->is_active ? 'check text-success' : 'times text-danger'}}"></i>
                                    </td>
                                    <td class="text-right">
                                        <div class="list-icons">
                                            <a href="{{route('tenant.transfers.show', [ 'id' => $transfer->id ])}}" class="list-icons-item text-grey"><i class="icon-pencil7"></i></a>
                                            @can('delete setting')
                                                <a href="{{route('tenant.transfers.remove', [ 'id' => $transfer->id ])}}" class="list-icons-item text-danger confirm-dialog" data-text="Delete this transfer?"><i class="icon-trash"></i></a>
                                            @endcan
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