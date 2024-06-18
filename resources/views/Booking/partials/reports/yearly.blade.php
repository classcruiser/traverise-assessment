<div class="page-content">
    <div class="content-wrapper">
        <div class="content">

            <div class="d-flex justify-content-between align-items-center my-3">
                <h3 class="mb-0">Yearly Overview</h3>

                <form action="/reports/yearly" method="get">
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
                        <div class="card-body p-3" style="height: 300px;">
                            <canvas id="yearly-chart" style="width: 100%; height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- LEFT -->
                @foreach($locations as $camp)
                    <?php $gtotal_mo[$camp->id] = []; ?>
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body p-0">
                                <table class="table table-xs table-report-custom table-striped">
                                    <thead>
                                        <tr class="bg-grey-700">
                                            <th>{{$camp->short_name}}</th>
                                            @foreach($months_col as $month)
                                                <th class="text-center">{{$month->format('M.y')}}</th>
                                            @endforeach
                                            <th class="text-center">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($months as $month)
                                            <?php
                                            $tmp_mo = new \Carbon\Carbon(date('Y-m-d', strtotime($month)));
                                            $total_mo = 0;
                                            ?>
                                            <tr>
                                                <td width="10%"><b>{{date('M Y', strtotime($month))}}</b></td>
                                                @foreach($months_col as $mo)
                                                    <?php
                                                    if (!array_key_exists($mo->format('Y-m'), $gtotal_mo[$camp->id])) {
                                                        $gtotal_mo[$camp->id][$mo->format('Y-m')] = 0;
                                                        $gtotal_mo[$camp->id]['total'] = 0;
                                                        }
                                                    ?>
                                                    <td class="text-center">
                                                        @if(
                                                            array_key_exists($camp->id, $data[$month]) &&
                                                            array_key_exists($mo->format('Y-m'), $data[$month][$camp->id])
                                                            )
                                                            <a href="/bookings?ref=&location= {{$camp->id}}&booking_date_from= {{$tmp_mo->startOfMonth()->format('Y-m-d')}}&booking_date_to= {{$tmp_mo->endOfMonth()->format('Y-m-d')}}&checkin_date_from= {{$mo->startOfMonth()->format('Y-m-d')}}&checkin_date_to= {{$mo->endOfMonth()->format('Y-m-d')}}" class="text-danger"><b>{{$data[$month][$camp->id][$mo->format('Y-m')]}}</b></a>

                                                            <?php
                                                            $total_mo += $data[$month][$camp->id][$mo->format('Y-m')];
                                                            $gtotal_mo[$camp->id][$mo->format('Y-m')] += $data[$month][$camp->id][$mo->format('Y-m')];
                                                            $gtotal_mo[$camp->id]['total'] += $data[$month][$camp->id][$mo->format('Y-m')];
                                                            ?>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="text-center">
                                                    <b><b>{{$total_mo}}</b></b>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td><b>TOTAL</b></td>
                                            @foreach($months_col as $mo)
                                                <td class="text-center">
                                                    <b>{{$gtotal_mo[$camp->id][$mo->format('Y-m')]}}</b>
                                                </td>
                                                <?php $chart_data[$camp->id] .= $gtotal_mo[$camp->id][$mo->format('Y-m')] .','; ?>
                                            @endforeach
                                            <td class="text-center">
                                                <b>{{$gtotal_mo[$camp->id]['total']}}</b>
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
    var ctx = document.getElementById('yearly-chart');
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
                    //data: [{{$chart_data[$camp->id]}}],
                    backgroundColor: '#{{$camp->color}}',
                    borderColor: '#{{$camp->color}}',
                    borderWidth: 2,
                    valuesType: 'pax',
                },
                @endforeach
            ],
            labels: @json($chart_labels)
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
