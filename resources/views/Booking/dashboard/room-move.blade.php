@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="py-1 px-2 d-flex justify-content-between align-items-center">
                            <h4><i class="fal fa-fw fa-random mr-1"></i> Room Move</h4>
                            <h4>{{date('D, d.m.Y', strtotime($today))}}</h4>
                        </div>
                        <div class="card">
                            <div class="card-body py-0 px-0">
                                <table class="table table-xs table-compact">
                                    <thead>
                                        <tr class="bg-grey-800">
                                            <th class="two wide">Ref</th>
                                            <th class="two wide">Guest</th>
                                            <th>Location</th>
                                            <th class="">In</th>
                                            <th>Current Room</th>
                                            <th class="">Out</th>
                                            <th>Next Room</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($data) >= 1)
                                            @foreach($data as $move)
                                                <tr>
                                                    <td><a href="{{route('tenant.bookings.show', [ 'ref' => $move['ref'] ])}}" title="" class="text-danger"><b>{{$move['ref']}}</b></a></td>
                                                    <td>{{$move['guest']}}</td>
                                                    <td>{{$move['camp']}}</td>
                                                    <td>{{$move['from']}}</td>
                                                    <td><b>{{$move['subroom']}}</b></td>
                                                    <td>{{$move['to']}}</td>
                                                    <td><b>{{$move['next_subroom']}}</b></td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7">No data</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
