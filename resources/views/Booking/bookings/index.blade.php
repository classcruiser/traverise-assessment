@extends('Booking.app') 

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item active">Bookings</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper">
            <div class="content">
                <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                    <h4 class="m-0 mr-auto d-none d-md-block"><i class="fal fa-fw fa-calendar-alt mr-1"></i> All Bookings</h4>
                    <button class="btn btn-labeled btn-labeled-left bg-orange-400 ml-1 collapsed" data-toggle="collapse" href="#advanced-search">
                        <b><i class="icon-search4"></i></b> Advanced Search
                    </button>
                    @can('add booking')
                        <a href="{{route('tenant.bookings.create')}}" class="btn btn-labeled btn-labeled-left bg-warning-700 ml-1">
                            <b><i class="icon-plus3"></i></b> New Booking
                        </a>
                    @endcan
                </div>
                @include('Booking.partials.bookings.advanced-search') 

                @if(request()->has('ref'))
                    <p>Booking total: <b>{{$bookings->count()}}</b></p>
                    <p>Pax total: <b>{{$bookings->sum(function ($b) { return $b->guests_count; })}}</b></p>
                @endif

                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-xs table-compact dark">
                            <thead>
                                <tr class="bg-grey-700">
                                    <th class="two wide">Ref</th>
                                    <th>Customer</th>
                                    <th class="text-center">Guests</th>
                                    <th>Location</th>
                                    <th>Addons</th>
                                    <th>Booked</th>
                                    <th>Check In / Out</th>
                                    <th>Price</th>
                                    <th>Paid</th>
                                    <th class="text-center">Commission</th>
                                    <th class="text-left">Channel</th>
                                    <th class="text-left">Opportunity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td valign="center" class="color-cell cell-{{$booking->status_badge}}">
                                            <div class="d-flex justify-content-start align-items-center">
                                                <div class="circle {{$booking->status_badge}}">&nbsp;</div>
                                                @if ($booking->agent_id)
                                                    <div class="circle bg-agent">&nbsp;</div>
                                                @endif
                                                <div>
                                                    <a href="{{route('tenant.bookings.show', [ 'ref' => $booking->ref ])}}" class="{{$booking->is_blacklisted ? 'text-grey-300' : 'text-dark'}}"><b>{{$booking->ref}}</b></a>
                                                    {!! $booking->is_blacklisted ? '<span class="tippy" data-tippy-content="Contains blacklisted guest"><i class="fa fa-ban text-danger"></i></span>' : '' !!}
                                                    {!! $booking->special_package_id ? '<span class="tippy" data-tippy-content="'. $booking->specialPackage->name .'"><b>SP</b></span>' : '' !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td><a href="{{route('tenant.guests.show', [ 'id' => $booking->guest->details->id ])}}" title="" class="text-danger"><b>{{$booking->guest->details->full_name}}</b></a></td>
                                        <td class="text-center">{{$booking->guests_count}}</td>
                                        <td>
                                            <span class="tippy" data-tippy-content="{{$booking->getAllRoomsName($role)}}"><b>{{$booking->location->name}}</b></span>
                                        </td>
                                        <td>{!! $booking->showAllAddons() !!}</td>
                                        <td>{{$booking->created_at->format('d.m.Y H:i:s')}}</td>
                                        <td><b>{{date('d.m.Y', strtotime($booking->check_in))}}</b> &mdash; <b>{{date('d.m.Y', strtotime($booking->check_out))}}</b></td>
                                        <td>
                                            @if(is_float($booking->payment->total) || is_int($booking->payment->total))
                                                <b>&euro;{{$booking->parsePrice($booking->payment->total)}}</b>
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-{{$booking->status_badge}}">
                                                <b>&euro;{{$booking->parsePrice($booking->payment->total_paid)}}</b>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span><b>{!! $booking->commission > 0 ? '&euro;'. number_format($booking->commission, 2) : '--' !!}</b></span>
                                        </td>
                                        <td class="text-left">{{$booking->channel}}</td>
                                        <td class="text-let"><span class="opportunity opportunity-{{$booking->opportunity}}">{{$booking->opportunity}}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-md-flex justify-content-between align-items-center">
                    <div>{{$bookings->appends($_GET)->links()}}</div>
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
    $('.daterange-empty').daterangepicker({
        autoApply: true,
        showDropdowns: true,
        minDate: "01/01/2018",
        minYear: 2018,
        maxYear: 2030,
        autoUpdateInput: false,
        locale: {
            format: 'DD.MM.YYYY'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'This Week': [moment().startOf('week'), moment().endOf('week')],
            'Last Week': [moment().startOf('week').subtract(7, 'days'), moment().endOf('week').subtract(7, 'days')],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'This Year': [moment().startOf('year'), moment().endOf('year')],
            'Last Year': [moment().startOf('year').subtract(1, 'year'), moment().endOf('year').subtract(1, 'year')],
        },
        alwaysShowCalendars: true,
    });
    $('.daterange-empty').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
    });

    $('.daterange-empty').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    </script>
@endsection