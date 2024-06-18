<div class="page-content">
    <div class="content-wrapper">
        <div class="content">

            <form action="/reports/accommodation" method="get">
                <div class="d-flex justify-content-start align-items-center my-3">
                    <h3 class="mb-0">Accommodation</h3>
                    <div style="width: 150px" class="ml-auto">
                        &nbsp;
                    </div>
                    <div style="width: 220px" class="ml-1">
                        <div class="input-group">
                            <span class="input-group-prepend">
                                <span class="input-group-text"><i class="icon-calendar22"></i></span>
                            </span>
                            <input type="text" class="form-control date-range" name="dates" value="{{$dates['default']}}" placeholder="select dates" />
                        </div>
                    </div>
                    @csrf
                    <button class="btn btn-labeled btn-labeled-left bg-danger ml-1 btn-sm">
                        <b><i class="icon-reset"></i></b> Update
                    </button>
                </div>
            </form>

            <div class="row">
                <!-- LEFT -->
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-0">
                            @foreach ($camps as $camp)
                                <div class="forecast-grid-entry">
                                    <div class="font-size-lg block px-4 py-3 text-uppercase mb-0"><b>{{ $camp->name }}</b></div>

                                    <table class="checkin">
                                        <thead>
                                            <tr>
                                                <th width="30%" rowspan="2">&nbsp;</th>
                                                @foreach ($period_dates as $date)
                                                    <th class="text-center text-uppercase">{{ $date['day'] }}</th>
                                                @endforeach
                                                <th class="text-center text-uppercase">nights booked</th>
                                            </tr>
                                            <tr>
                                                @foreach ($period_dates as $date)
                                                    <th class="text-center">{{ $date['title'] }}</th>
                                                @endforeach
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($camp->rooms as $room)
                                                <tr>
                                                    <td>{{ $room->name }}</td>
                                                    @foreach ($period_dates as $date)
                                                        <td class="text-center">
                                                            @if (isset($data[$room->id][$date['date']]))
                                                                {{ $data[$room->id][$date['date']] }}
                                                            @else
                                                                0
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    <td class="text-center font-weight-bold">
                                                        @if (isset($aggregates['booked_nights'][$room->id]))
                                                            {{ $aggregates['booked_nights'][$room->id] }}
                                                        @else
                                                            0
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td><b>TOTAL</b></td>
                                                @foreach ($period_dates as $date)
                                                    <td class="text-center font-weight-bold">
                                                        @if (isset($aggregates['total_per_dates'][$date['date']]))
                                                            {{ $aggregates['total_per_dates'][$date['date']] }}
                                                        @else
                                                            0
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="text-center font-weight-bold">
                                                    @if (isset($aggregates['total']))
                                                        {{ $aggregates['total'] }}
                                                    @else
                                                        0
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Daily expected check ins</td>
                                                @foreach ($check_ins as $date => $check_in)
                                                    <td class="text-center">{{ $check_in['expected'] }}</td>
                                                @endforeach
                                                <td class="text-center">{{ $aggregates['expected'] }}</td>
                                            </tr>
                                            <tr>
                                                <td>Checked in</td>
                                                @foreach ($check_ins as $date => $check_in)
                                                    <td class="text-center">{{ $check_in['checked_in'] }}</td>
                                                @endforeach
                                                <td class="text-center">{{ $aggregates['checked_in'] }}</td>
                                            </tr>
                                            <tr>
                                                <td>No show</td>
                                                @foreach ($check_ins as $date => $check_in)
                                                    <td class="text-center">{{ $check_in['no_show'] }}</td>
                                                @endforeach
                                                <td class="text-center">{{ $aggregates['no_show'] }}</td>
                                            </tr>
                                            <tr>
                                                <td>Daily expected check outs</td>
                                                @foreach ($check_ins as $date => $check_in)
                                                    <td class="text-center">{{ $check_in['checked_out'] }}</td>
                                                @endforeach
                                                <td class="text-center">{{ $aggregates['checked_out'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@section('scripts')
@endsection
