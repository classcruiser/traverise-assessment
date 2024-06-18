@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item active">Reports</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper">
            <div class="content">
                <div class="px-3 d-flex justify-content-end align-items-start mb-3">
                    <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-files mr-1"></i> Reports</h4>
                    <form action="{{route('tenant.classes.reports.index')}}" method="get">
                        <div class="row">
                            <div class="col-sm-12 d-flex">
                                <div style="width: 300px;" class="mr-1">
                                    <select class="form-control select-no-search form-control-sm mr-1" data-container-css-class="select-sm" data-fouc data-placeholder="Category" name="category_filter">
                                        @foreach ($class_categories as $category)
                                            <option value="category-{{$category->id}}" {{request('category_filter') == 'category-'. $category->id ? 'selected' : ''}}>{{$category->name}}</option>
                                            @if ($category->sessions)
                                                @foreach ($category->sessions as $subcategory)
                                                    <option value="session-{{$subcategory->id}}" {{request('category_filter') == 'session-'. $subcategory->id ? 'selected' : ''}}>
                                                        &nbsp;-&nbsp; {{$subcategory->name}}
                                                    </option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div style="width: 220px" class="ml-1">
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                        </span>
                                        <input type="text"
                                               class="form-control form-control-sm date-range"
                                               name="dates"
                                               id="check-in-daterange"
                                               value="{{request('dates')}}"
                                               placeholder="Date period"/>
                                    </div>
                                </div>
                                <button class="btn btn-danger btn-sm ml-1" type="submit">OK</button>
                                @if (request()->has('category_filter') || request()->has('dates'))
                                    <a class="btn btn-success btn-sm ml-1" href="{{ url()->full() }}&export">EXPORT</a>
                                @endif
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
                                <th>Day</th>
                                <th class="text-left">Date</th>
                                <th class="text-left">Time</th>
                                <th class="text-center">Space</th>
                                <th class="text-center">Booked</th>
                                <th class="text-center">Open</th>
                                <th class="text-center">Percentage</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse ($data['schedules'] as $session)
                                    <tr>
                                        <td class="color-cell session-{{ $session['color_code'] }}">
                                            <b>{{$session['title']}}</b>
                                        </td>
                                        <td>{{$session['day']}}</td>
                                        <td class="text-left">{{ $session['date'] ? Carbon\Carbon::createFromFormat('Y-m-d', $session['date'])->format('d.m.Y') : '-' }}</td>
                                        <td class="text-left">{{ $session['start_formatted'] }} - {{ $session['end_formatted'] }}</td>
                                        <td class="text-center">{{ $session['max_pax'] }}</td>
                                        <td class="text-center">{{ $session['current_pax'] }}</td>
                                        <td class="text-center">{{ $session['max_pax'] - $session['current_pax'] }}</td>
                                        <td class="text-center font-weight-bold">{{ $session['percentage'] }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No data available</td>
                                    </tr>
                                @endforelse
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
