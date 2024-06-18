@extends('Booking.app') 

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <span class="breadcrumb-item active">Agents</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h4 class="card-title">Agents</h4>
                        <div class="header-elements">
                            @can('manage agent')
                                <a href="{{route('tenant.agents.create')}}" title="" class="btn bg-danger">
                                    <i class="far fa-plus mr-1"></i> New Agent
                                </a>
                            @endcan
                        </div>
                    </div>
                    <table class="table table-xs table-compact">
                        <thead>
                            <tr class="bg-grey-700">
                                <th>Name</th>
                                <th>Email</th>
                                <th class="text-right">Commission</th>
                                <th class="text-center">Active</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agents as $agent)
                                <tr>
                                    <td class="vertical-top"><a href="{{route('tenant.agents.show', [ 'id' => $agent->id ])}}" class="list-icons-item text-danger"><b>{{$agent->name}}</b></a></td>
                                    <td><code>{{$agent->email}}</code></td>
                                    <td class="text-right font-weight-bold">{{$agent->commission_value}}%</td>
                                    <td class="text-center">
                                        <i class="far fa-fa fa-{{$agent->active ? 'check text-success' : 'times text-danger'}}"></i>
                                    </td>
                                    <td class="text-right">
                                        <div class="list-icons">
                                            <a href="{{route('tenant.agents.show', [ 'id' => $agent->id ])}}" class="list-icons-item text-grey"><i class="icon-pencil7"></i></a>
                                            @can('delete setting')
                                                <a href="{{route('tenant.agents.delete', [ 'id' => $agent->id ])}}" class="list-icons-item text-danger confirm-dialog" data-text="Delete this agent?"><i class="icon-trash"></i></a>
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