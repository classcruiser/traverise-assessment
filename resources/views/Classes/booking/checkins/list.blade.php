@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item active">Check-Ins</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper">
            <div class="content">
                <div class="px-3 d-flex justify-content-end align-items-start mb-3">
                    <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-sign-in-alt mr-1"></i> Check-Ins</h4>
                    <form action="{{route('tenant.classes.bookings.check-ins')}}" method="get">
                        <div class="row">
                            <div class="col-sm-12 d-flex">
                                <div style="width: 220px" class="ml-1">
                                    <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="icon-calendar22"></i>
                                                    </span>
                                                </span>
                                        <input type="text"
                                               class="form-control form-control-sm date-range"
                                               name="check_in_dates"
                                               id="check-in-daterange"
                                               value="{{request('check_in_dates')}}"
                                               placeholder="Check In dates"/>
                                    </div>
                                </div>
                                <button class="btn btn-danger btn-sm ml-1" type="submit">OK</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-xs table-compact dark">
                            <thead>
                            <tr class="bg-grey-700">
                                <th>Name</th>
                                <th>Ref</th>
                                <th>Session</th>
                                <th class="text-left">Date</th>
                                <th class="text-left">Time</th>
                                <th class="text-center">Arrived</th>
                                <th class="text-right">Arrived At</th>
                                <th class="text-right">Checked in by</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sessions as $session)
                                <tr class="check-in-{{ $session->booking->booking_status }}">
                                    <td class="text-left">
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="circle {{ $session->booking->status_badge }} tippy" data-tippy-content="{{ $session->booking->status }}">&nbsp;</div>
                                            <div>
                                                <a href="#" class="text-dark text-uppercase"><b>{{ $session->full_name }}</b></a>
                                            </div>
                                        </div>
                                    </td>
                                    <td valign="center">
                                        <div>
                                            <a href="{{ route('tenant.classes.bookings.show', [ 'ref' => $session->booking->ref ]) }}"
                                               class="text-dark"><b>{{ $session->booking->ref }}</b></a>
                                        </div>
                                    </td>
                                    <td valign="center">
                                        <div>
                                            <a href="{{ route('tenant.classes.sessions.show', [ 'id' => $session->session->id ]) }}"
                                               class="text-dark"><b>{{ $session->session->name }}</b></a>
                                        </div>
                                    </td>
                                    <td class="text-left">{{ $session->date->format('d.m.Y') }}</td>
                                    <td class="text-left">{{ $session->schedule->start_formatted .' - '.$session->schedule->end_formatted }}</td>
                                    <td class="text-center">
                                        @if($session->booking->has_check_in)
                                            <i class="fas fa-fw fa-check text-success"></i>
                                        @else
                                            <i class="fas fa-fw fa-times text-danger"></i>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ $session->booking->checked_in_at ? $session->booking->checked_in_at->format('d.m.Y H:i:s') : '-' }}</td>
                                    <td class="text-right">{{ $session->booking->checked_in_at ? $session->booking->checkedInUser->name : '-' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $('#check-in-daterange').daterangepicker({
            autoApply: true,
            showDropdowns: true,
            minDate: "01/01/2018",
            minYear: 2018,
            maxYear: 2040,
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
        $('#check-in-daterange').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
        });

        $('#check-in-daterange').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });
    </script>
@endsection
