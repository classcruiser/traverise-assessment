@extends('Booking.app') 

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <span class="breadcrumb-item active">Automated Emails</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                    <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-envelope-open mr-1"></i> Automated Emails</h4>
                    @can('add automated email')
                        <a href="{{route('tenant.automated-emails.create')}}" title="" class="btn bg-danger">
                            <i class="far fa-plus mr-1"></i> New Email
                        </a>
                    @endcan
                </div>
                <div class="card">
                    <table class="table table-xs table-compact">
                        <thead>
                            <tr class="bg-grey-700">
                                <th>Name</th>
                                <th class="text-center">Scheduled</th>
                                <th>Time</th>
                                <th>Timing</th>
                                <th>Column</th>
                                <th>Template</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($emails as $email)
                                <tr>
                                    <td><a href="{{route('tenant.automated-emails.show', [ 'id' => $email->id ])}}" class="text-danger"><b>{{$email->name}}</b></a></td>
                                    <td class="text-center">
                                        {!! $email->is_scheduled ? '<i class="far fa-fw fa-check text-success"></i>' : '<i class="fal fa-fw fa-times text-danger"></i>' !!}
                                    </td>
                                    <td>{{$email->is_scheduled ? $email->send_time .' '. $email->time_unit : '-'}}</td>
                                    <td>{{$email->is_scheduled ? $email->send_timing : '-'}}</td>
                                    <td><code>{{$email->is_scheduled ? $email->send_date_column : '-'}}</code></td>
                                    <td><code>{{$email->resource}}</code></td>
                                    <td class="text-right">
                                        <div class="list-icons">
                                            <a href="{{route('tenant.automated-emails.show', $email->id)}}" class="list-icons-item text-primary"><i class="icon-pencil7"></i></a>
                                            @can('delete automated email')
                                                <a href="{{route('tenant.automated-emails.delete', $email->id)}}" class="list-icons-item text-danger confirm-dialog" data-text="Delete email?">
                                                    <i class="icon-trash"></i>
                                                </a>
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