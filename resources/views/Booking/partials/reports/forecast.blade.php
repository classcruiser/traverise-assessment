<div class="page-content">
    <div class="content-wrapper">
        <div class="content">

            <form action="/reports/forecast" method="get">
                <div class="d-flex justify-content-start align-items-center my-3">
                    <h3 class="mb-0">Room Occupancy</h3>
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
                        <div class="card-header header-elements-inline bg-transparent">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h5 class="card-title"><b>Occupancy {{$camp_name != 'all' ? ': '. $camp_name : ''}}</b></h5>
                                <h5 class="mb-0 text-grey"><i class="fa fa-fw fa-mr-1 fa-chart-line"></i> Average: {{$result['total_average']}}%</h5>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="text-center">
                                <h5>{{$dates['default']}}</h5>
                            </div>
                            <div>
                                <canvas id="occupancy" style="width: 100%; height: 350px;"></canvas>
                            </div>
                        </div>
                        <script>
                        var ctx = document.getElementById('occupancy');
                        var chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                datasets: [{
                                    label: 'Occupancy',
                                    fill: false,
                                    yAxisID: 'y-axis-1',
                                    data: @json($result['chart_data']),
                                    //data: [],
                                    backgroundColor: '#ace46b',
                                    borderColor: '#ace46b',
                                    borderWidth: 2,
                                    valuesType: 'percentage',
                                }, {
                                    label: 'Average',
                                    fill: false,
                                    yAxisID: 'y-axis-1',
                                    data: @json($result['chart_average']),
                                    //data: [],
                                    radius: 0,
                                    backgroundColor: '#999999',
                                    borderColor: '#999999',
                                    borderWidth: 2,
                                    valuesType: 'percentage',
                                }],
                                labels: @json($result['chart_labels'])
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
                                            if (valuesType == 'percentage') {
                                                value = value + '%';
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
                                            min: 0,
                                            max: 100,
                                            step: 10,
                                            callback: function(value) {
                                                if (Number.isInteger(value)) {
                                                    return value + '%';
                                                }
                                            }
                                        }
                                    }],
                                }
                            },
                        })
                        </script>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header header-elements-inline bg-transparent">
                            <h5 class="card-title"><b>Room Occupancy</b></h5>
                        </div>
                        <div class="card-body p-3">

                            <div class="forecast-grid">
                                @foreach ($camps as $camp)
                                    <div class="forecast-grid-entry">
                                        <p class="font-size-lg"><b>{{ $camp->name }}</b></p>

                                        <table class="occupancy">
                                            <tr>
                                                <th width="70%">Room Category</th>
                                                <th width="10%">Beds</th>
                                                <th width="10%">Guests</th>
                                                <th width="10%">Percentage</th>
                                            </tr>
                                            @if(in_array($camp->id, $allowed_camps))
                                                @foreach($camp->rooms as $room)
                                                    <tr>
                                                        <td>{{$room->name}}</td>
                                                        <td>{{$result['rooms'][$room->id]['beds']}}</td>
                                                        <td>{{$result['rooms'][$room->id]['guests']}}</td>
                                                        <td>{{$result['rooms'][$room->id]['average']}}%</td>
                                                    </tr>
                                                @endforeach
                                            @endif
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
</div>
@section('scripts')
@endsection
