@extends('Booking.app') 

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item active">Deleted Bookings</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper">
            <div class="content">
                <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                    <h4 class="m-0 mr-auto"><i class="fa fa-fw fa-trash mr-1"></i> Deleted Bookings</h4>
                    <button class="btn btn-labeled btn-labeled-left bg-orange-400 ml-1 collapsed" data-toggle="collapse" href="#advanced-search">
                        <b><i class="icon-search4"></i></b> Advanced Search
                    </button>
                </div>
                <div id="advanced-search" class="collapse {{request()->has('ref') ? 'show' : ''}}">
                    <div class="px-3 d-flex justify-content-end align-items-start mb-3">
                        <form action="{{route('tenant.bookings.trash')}}" method="get">
                            <div style="width: 680px;" class="p-3 border-1 border-alpha-grey">

                                <div class="row">
                                    <div class="col-sm-4 mb-3">
                                        <label>Booking reference number</label>
                                        <input type="text" name="ref" class="form-control form-control-sm" placeholder="Booking reference number" value="{{request('ref')}}" />
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <label>Guest name</label>
                                        <input type="text" name="guest_name" class="form-control form-control-sm" placeholder="Guest name" value="{{request('guest_name')}}" />
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <label>Email address</label>
                                        <input type="text" name="email" class="form-control form-control-sm" placeholder="Email address" value="{{request('email')}}" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Cancellation date start</label>
                                        <input type="text" name="cancel_date_start" autocomplete="off" class="form-control form-control-sm date-basic" placeholder="Cancellation date start" value="{{request('cancel_date_start')}}" />
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Cancellation date end</label>
                                        <input type="text" name="cancel_date_end" autocomplete="off" class="form-control form-control-sm date-basic" placeholder="Cancellation date end" value="{{request('cancel_date_end')}}" />
                                    </div>
                                    <div class="col-sm-4">

                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group mb-0">
                                            <label>&nbsp;</label>
                                            <a href="/bookings/cancelled" title="" class="btn bg-grey btn-sm d-block">Reset search</a>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="form-group mb-0">
                                            <label>&nbsp;</label>
                                            {!! csrf_field() !!}
                                            <input type="hidden" name="status" value="CANCELLED" />
                                            <button class="btn d-block w-100 bg-danger btn-sm">Search</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <table class="table table-xs table-compact dark">
                        <thead>
                            <tr class="bg-grey-700">
                                <th class="two wide">Ref</th>
                                <th>Guest</th>
                                <th class="two wide">Location</th>
                                <th>Check In / Out</th>
                                <th>Price</th>
                                <th class="text-left">Reason</th>
                                <th class="text-right">By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        <a href="/bookings/{{$booking->ref}}" class="text-danger"><b>{{$booking->ref}}</b></a>
                                        {!! $booking->special_package_id ? '<span class="tippy" data-tippy-content="'. $booking->specialPackage->name .'"><b>SP</b></span>' : '' !!}
                                    </td>
                                    <td>
                                        @if ($booking->guest)
                                            <a href="/guest/{{$booking->guest->details->id}}" title="" class="text-danger"><b>{{$booking->guest->details->full_name}}</b></a>
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    <td>
                                        @if ($booking->location)
                                            <span class="tippy" data-tippy-content="{{$booking->getAllRoomsName()}}"><b>{{$booking->location->short_name}}</b></span>
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    <td><b>{{date('d.m.Y', strtotime($booking->check_in))}}</b> &mdash; <b>{{date('d.m.Y', strtotime($booking->check_out))}}</b></td>
                                    <td><b>&euro;{{floatVal(round($booking->grand_total, 2))}}</b></td>
                                    <td class="text-left">{{$booking->cancel_reason}}</td>
                                    <td class="text-right">{{$booking->histories()?->latest()?->first()?->user?->name}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>{{$bookings->appends($_GET)->links()}}</div>
                    <div>
                        <a href="{{(request()->fullUrl()) . (request()->has('_token') ? '&' : '?')}}export=true" title="" class="btn btn-success">
                            <i class="fa fa-fw fa-file-excel"></i> Export to Excel
                        </a>
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
    });
    $('.date-basic').daterangepicker({
        autoApply: false,
        autoUpdateInput: false,
        singleDatePicker: true,
        locale: {
            format: 'DD.MM.YYYY',
            cancelLabel: "Clear"
        }
    });
    $('.date-basic').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD.MM.YYYY'));
    });
    </script>
@endsection