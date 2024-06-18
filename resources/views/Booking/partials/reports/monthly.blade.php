<div class="page-content">
    <div class="content-wrapper">
        <div class="content">

            <div class="d-flex justify-content-between align-items-center my-3">
                <h3 class="mb-0">Monthly Overview</h3>

                <form action="/reports/monthly" method="get">
                    <div class="select-period d-flex justify-content-end align-items-center">
                        <span class="ml-auto">CAMP</span>
                        <div style="width: 220px;" class="ml-2">
                            <select class="form-control multiselect" name="camps[]" multiple="multiple" data-fouc>
                                @foreach($locations as $camp)
                                    <option value="{{$camp->id}}" {{!request()->has('camps') || in_array($camp->id, request('camps')) ? 'selected' : ''}}>{{$camp->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="width: 70px" class="ml-1">
                            <select name="date_y" class="form-control">
                                @for($i = 2019; $i <= 2025; $i++)
                                    <option value="{{$i}}" {{$date_y == $i ? 'selected' : ''}}>{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                        <div style="width: 100px" class="ml-1">
                            <select name="date_m" class="form-control">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{$i}}" {{$date_m == $i ? 'selected' : ''}}>{{date('F', strtotime(date('Y') .'-'. $i .'-01'))}}</option>
                                @endfor
                            </select>
                        </div>
                        @csrf
                        <button class="btn btn-labeled btn-labeled-left bg-danger ml-1 btn-sm" type="submit">
                            <b><i class="icon-loop3"></i></b> Update
                        </button>
                    </div>
                </form>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-3" style="max-height: 300px;">
                            <canvas id="monthly-chart" style="width: 100%; height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- LEFT -->
                @foreach($selected_camps as $location)
                    <div class="col-sm-12">
                        <h5>{{$location->name}}</h5>
                        <div class="card">
                            <div class="card-body p-0">
                                <table class="table table-xs table-report-custom table-striped">
                                    <thead>
                                        <tr class="bg-grey-700">
                                            <th>Date</th>
                                            @foreach($months as $month)
                                                <th class="text-center">{{$month['label']}}</th>
                                            @endforeach
                                            <th class="text-center">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dates as $date)
                                            <tr>
                                                <td width="10%"><b>{{$date['label']}}</b></td>
                                                @foreach($months as $month)
                                                    <td class="text-center">
                                                        @if(
                                                            array_key_exists($location->id, $data[$date['key']]) &&
                                                            array_key_exists($month['key'], $data[$date['key']][$location->id])
                                                            )
                                                            <a href="/bookings?ref=&location= {{$location->id}}&booking_date_from= {{$date['key']}}&booking_date_to= {{$date['key']}}&checkin_date_from= {{$month['min_date']}}&checkin_date_to= {{$month['max_date']}}" class="text-danger"><b>{{$data[$date['key']][$location->id][$month['key']]}}</b></a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="text-center">
                                                    <b>{{array_key_exists($location->id, $data[$date['key']]) ? $data[$date['key']][$location->id]['total'] : '-'}}</b>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td><b>TOTAL</b></td>
                                            @foreach($months as $month)
                                                <td class="text-center">
                                                    @if(
                                                        array_key_exists($location->id, $result) &&
                                                        array_key_exists($month['key'], $result[$location->id])
                                                        )
                                                        <b>{{$result[$location->id][$month['key']]}}</b>
                                                        <?php $chart_data[$location->id] .= $result[$location->id][$month['key']] .','; ?>
                                                    @else
                                                        <?php $chart_data[$location->id] .= '0,'; ?>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="text-center">
                                                @if(
                                                    array_key_exists($location->id, $result) &&
                                                    array_key_exists('total', $result[$location->id])
                                                    )
                                                    <b>{{$result[$location->id]['total']}}</b>
                                                @endif
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

    </div>
</div>

@section('scripts')
    <script>
    var ctx = document.getElementById('monthly-chart');
    var chart = new Chart(ctx, {
        type: 'line',
        multiTooltipTemplate: "<%= datasetLabel %> : <%= value %>",
        data: {
            datasets: [
                @foreach ($selected_camps as $index => $camp)
                {
                    label: '{{$camp->short_name}}',
                    fill: false,
                    yAxisID: 'y-axis-1',
                    data: [{{substr($chart_data[$camp->id], 0, -1)}}],
                    backgroundColor: '#{{$camp->color}}',
                    borderColor: '#{{$camp->color}}',
                    borderWidth: 2,
                    valuesType: 'pax',
                },
                @endforeach
            ],
            labels: @json($chart['labels'])
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            tooltips: {
                callbacks: {
                    label: function(tooltipItems, data) {
                        var dataset = data.datasets[tooltipItems.datasetIndex];
                        var valuesType = dataset.valuesType;
                        var label = dataset.label;
                        var value = tooltipItems.yLabel.toLocaleString();
                        if (valuesType == 'pax') {
                            value = value + ' pax';
                        }
                        return label + ': ' + value;
                    }
                }
            },
            scales: {
                yAxes: [{
                    type: 'linear',
                    display: true,
                    position: 'left',
                    id: 'y-axis-1',
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            if (Number.isInteger(value)) {
                                return value;
                            }
                        },
                    }
                }],
            }
        },
    })
    $('.multiselect').multiselect({
        nonSelectedText: 'Select Camp',
        includeSelectAllOption: true,
    });
    </script>
@endsection
