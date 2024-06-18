<div class="page-content">
    <div class="content-wrapper">
        <div class="content">

            <form action="/reports" method="get">
                <div class="d-flex justify-content-start align-items-center my-3">
                    <h3 class="mb-0">Reports</h3>
                    <span class="ml-auto">CAMP</span>
                    <div style="width: 220px;" class="ml-2">
                        <select class="form-control multiselect" name="camps[]" multiple="multiple" data-fouc>
                            @foreach($locations as $camp)
                                <option value="{{$camp->id}}" {{!request()->has('camps') || in_array($camp->id, request('camps')) ? 'selected' : ''}}>{{$camp->name}}</option>
                            @endforeach
                        </select>
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
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-header header-elements-inline bg-transparent">
                            <h5 class="card-title"><b>Total Opportunities</b></h5>
                        </div>
                        <div class="card-body p-3">
                            <h5 class="text-center"><b>&euro;{{number_format($total_opportunities_pie_data['total'], 2)}}</b></h5>
                            <div style="width: 100%; height: 250px;"><canvas id="total-opportunities-pie" style="width: 100%; height: 250px;"></canvas></div>
                        </div>
                        <script>
                        var oppCtx = document.getElementById('total-opportunities-pie');
                        var totalOppPie = new Chart(oppCtx, {
                            type: 'pie',
                            data: {
                                datasets: [{
                                    "data": [
                                        @json($total_opportunities_pie_data['data']['Dashboard']),
                                        @json($total_opportunities_pie_data['data']['Online']),
                                        @json($total_opportunities_pie_data['data']['ChannelManager']),
                                        @json($total_opportunities_pie_data['data']['Pending']),
                                        @json($total_opportunities_pie_data['data']['Abandoned']),
                                    ],
                                    "backgroundColor": [
                                        "#76d4ff", "#ace46b", "#bb6bff", "#ff9900", "#242424"
                                    ]
                                }],
                                labels: [
                                    'Dashboard',
                                    'Online',
                                    'Channel Manager',
                                    'Pending',
                                    'Abandoned'
                                ]
                            },
                            options: {
                                "responsive": true,
                                "maintainAspectRatio": false,
                                "tooltips": {
                                    "callbacks": {
                                        "label": function(tooltipItems, data) {
                                            var value = data.datasets[0].data[tooltipItems.index].toLocaleString();
                                            return data.labels[tooltipItems.index] + ': €' + value;
                                        }
                                    }
                                }
                            }
                        })
                        </script>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-header header-elements-inline bg-transparent">
                            <h5 class="card-title"><b>Confirmed Sales</b></h5>
                        </div>
                        <div class="card-body p-3">
                            <h5 class="text-center"><b>&euro;{{number_format($confirmed_sales_pie_data['total'], 2)}}</b></h5>
                            <div style="width: 100%; height: 250px;"><canvas id="confirmed-sales-pie" style="width: 100%; height: 250px;"></canvas></div>
                        </div>
                        <script>
                        var confirmedCtx = document.getElementById('confirmed-sales-pie');
                        var totalConfirmedPie = new Chart(confirmedCtx, {
                            type: 'pie',
                            data: {
                                datasets: [{
                                    "data": [
                                        @json($confirmed_sales_pie_data['data']['Dashboard']),
                                        @json($confirmed_sales_pie_data['data']['Online']),
                                        @json($confirmed_sales_pie_data['data']['ChannelManager']),
                                        @json($confirmed_sales_pie_data['data']['Pending']),
                                    ],
                                    "backgroundColor": [
                                        "#76d4ff", "#ace46b", "#bb6bff", "#ff9900"
                                    ]
                                }],
                                labels: [
                                    'Dashboard',
                                    'Online',
                                    'Channel Manager',
                                    'Pending',
                                ]
                            },
                            options: {
                                "responsive": true,
                                "maintainAspectRatio": false,
                                "tooltips": {
                                    "callbacks": {
                                        "label": function(tooltipItems, data) {
                                            var value = data.datasets[0].data[tooltipItems.index].toLocaleString();
                                            return data.labels[tooltipItems.index] + ': €' + value;
                                        }
                                    }
                                }
                            }
                        })
                        </script>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-header header-elements-inline bg-transparent">
                            <div class="d-flex justify-content-between w-100 align-items-center">
                                <h5 class="card-title"><b>Confirmed Sales - conversion rates</b></h5>
                                <h5 class="mb-0 text-grey"><i class="fa fa-chart-line mr-1"></i> Average: {{$overall_average[0]}}%</h5>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div style="width: 100%; height: 286px;"><canvas id="sales-conversion" style="width: 100%; height: 286px;"></canvas></div>
                        </div>
                        <script>
                        var conversionCtx = document.getElementById('sales-conversion');
                        var conversionRatesPie = new Chart(conversionCtx, {
                            type: 'bar',
                            data: {
                                datasets: [{
                                    label: 'Conversion Rate',
                                    yAxisID: 'y-axis-2',
                                    data: @json($avg_conversion_rates),
                                    type: "line",
                                    fill: false,
                                    valuesType: 'percentage',
                                    borderColor: "#a4d270",
                                    borderWidth: 2,
                                    backgroundColor: "#a4d270"
                                }, {
                                    label: 'Average',
                                    yAxisID: 'y-axis-2',
                                    data: @json($overall_average),
                                    type: "line",
                                    fill: false,
                                    valuesType: 'percentage',
                                    average:{{$overall_average[0]}},
                                    radius: 0,
                                    borderColor: "#999999",
                                    borderWidth: 2,
                                    backgroundColor: "#999999"
                                }, {
                                    label: 'Total Opportunities',
                                    yAxisID: 'y-axis-1',
                                    data: @json($pending_conversion_rates),
                                    backgroundColor: '#dddddd',
                                    valuesType: 'currency',
                                }, {
                                    label: 'Confirmed',
                                    yAxisID: 'y-axis-1',
                                    data: @json($confirmed_conversion_rates),
                                    backgroundColor: '#76d4ff',
                                    valuesType: 'currency',
                                }],
                                labels: @json($labels_conversion_rates)
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
                                            if (valuesType == 'currency') {
                                                value = '€' + value;
                                            } else if (valuesType == 'percentage') {
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
                                            callback: function(value) {
                                                if (Number.isInteger(value)) {
                                                    return '€' + value.toLocaleString();
                                                }
                                            },
                                        },
                                        gridLines: {
                                            display: false
                                        }
                                    }, {
                                        type: 'linear',
                                        display: true,
                                        position: 'right',
                                        id: 'y-axis-2',
                                        ticks: {
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

                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-header header-elements-inline bg-transparent">
                            <div class="d-flex justify-content-between w-100 align-items-center">
                                <h5 class="card-title"><b>Confirmed Sales - breakdown by date</b></h5>
                                <h5 class="mb-0 text-grey"><i class="fa fa-chart-line mr-1"></i> Average: &euro;{{number_format(round($csb['average'], 2), 2)}}</h5>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div style="width: 100%; height: 286px;"><canvas id="confirmed-sales-breakdown" style="width: 100%; height: 286px;"></canvas></div>
                        </div>
                        <script>
                        var cfbCtx = document.getElementById('confirmed-sales-breakdown');
                        var cfbChart = new Chart(cfbCtx, {
                            type: 'bar',
                            data: {
                                datasets: [{
                                    label: 'Total',
                                    yAxisID: 'y-axis-1',
                                    data: @json($csb['total']),
                                    type: "line",
                                    fill: false,
                                    valuesType: 'currency',
                                    borderColor: "#a4d270",
                                    borderWidth: 2,
                                    backgroundColor: "#a4d270"
                                }, {
                                    label: 'Average',
                                    yAxisID: 'y-axis-1',
                                    data: @json($csb['overall_average']),
                                    type: "line",
                                    fill: false,
                                    valuesType: 'currency',
                                    average:{{$csb['average']}},
                                    radius: 0,
                                    borderColor: "#999999",
                                    borderWidth: 2,
                                    backgroundColor: "#999999"
                                }, {
                                    label: 'Dashboard',
                                    yAxisID: 'y-axis-1',
                                    data: @json($csb['dashboard']),
                                    backgroundColor: '#76d4ff',
                                    borderColor: '#76d4ff',
                                    borderWidth: 2,
                                    valuesType: 'currency',
                                }, {
                                    label: 'Online',
                                    yAxisID: 'y-axis-1',
                                    data: @json($csb['online']),
                                    backgroundColor: '#ace46b',
                                    borderColor: '#ace46b',
                                    borderWidth: 2,
                                    valuesType: 'currency',
                                }, {
                                    label: 'Pending',
                                    yAxisID: 'y-axis-1',
                                    data: @json($csb['pending']),
                                    backgroundColor: '#ff9900',
                                    borderColor: '#ff9900',
                                    borderWidth: 2,
                                    valuesType: 'currency',
                                }, {
                                    label: 'Channel Manager',
                                    yAxisID: 'y-axis-1',
                                    data: @json($csb['channel']),
                                    backgroundColor: '#bb6bff',
                                    borderColor: '#bb6bff',
                                    borderWidth: 2,
                                    valuesType: 'currency',
                                }],
                                labels: @json($csb['labels'])
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
                                            if (valuesType == 'currency') {
                                                value = '€' + value;
                                            } else if (valuesType == 'percentage') {
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
                                            callback: function(value) {
                                                if (Number.isInteger(value)) {
                                                    return '€' + value.toLocaleString();
                                                }
                                            },
                                        }
                                    }],
                                }
                            },
                        })
                        </script>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-header header-elements-inline bg-transparent">
                            <h5 class="card-title"><b>Confirmed Sales - breakdown by location</b></h5>
                        </div>
                        <div class="card-body p-3">
                            <div style="width: 100%; height: 286px;"><canvas id="confirmed-sales-location" style="width: 100%; height: 286px;"></canvas></div>
                        </div>
                        <script>
                        var cflCtx = document.getElementById('confirmed-sales-location');
                        var cflChart = new Chart(cflCtx, {
                            type: 'line',
                            data: {
                                datasets: [
                                    @foreach ($selected_camps as $index => $camp)
                                    {
                                        label: '{{$camp->short_name}}',
                                        fill: false,
                                        yAxisID: 'y-axis-1',
                                        data: @json($csl[($index + 1)]),
                                        backgroundColor: '#{{$camp->color}}',
                                        borderColor: '#{{$camp->color}}',
                                        borderWidth: 2,
                                        valuesType: 'currency',
                                    },
                                    @endforeach
                                ],
                                labels: @json($csl['labels'])
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
                                            if (valuesType == 'currency') {
                                                value = '€' + value;
                                            } else if (valuesType == 'percentage') {
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
                                            callback: function(value) {
                                                if (Number.isInteger(value)) {
                                                    return '€' + value.toLocaleString();
                                                }
                                            },
                                        }
                                    }],
                                }
                            },
                        })
                        </script>
                    </div>
                </div>

            </div>

            <div class="row">
                <!-- LEFT -->
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-header header-elements-inline bg-transparent">
                            <h5 class="card-title"><b>Total Opportunities</b></h5>
                        </div>
                        <div class="card-body p-3">
                            <h5 class="text-center"><b>{{$total_opportunities_pax_pie_data['total']}} guests</b></h5>
                            <div style="width: 100%; height: 250px;"><canvas id="total-opportunities-pax-pie" style="width: 100%; height: 250px;"></canvas></div>
                        </div>
                        <script>
                        var oppPaxCtx = document.getElementById('total-opportunities-pax-pie');
                        var totalOppPaxPie = new Chart(oppPaxCtx, {
                            type: 'pie',
                            data: {
                                datasets: [{
                                    "data": [
                                        @json($total_opportunities_pax_pie_data['data']['Dashboard']),
                                        @json($total_opportunities_pax_pie_data['data']['Online']),
                                        @json($total_opportunities_pax_pie_data['data']['ChannelManager']),
                                        @json($total_opportunities_pax_pie_data['data']['Pending']),
                                        @json($total_opportunities_pax_pie_data['data']['Abandoned']),
                                    ],
                                    "backgroundColor": [
                                        "#76d4ff", "#ace46b", "#bb6bff", "#ff9900", "#242424"
                                    ]
                                }],
                                labels: [
                                    'Dashboard',
                                    'Online',
                                    'Channel Manager',
                                    'Pending',
                                    'Abandoned'
                                ]
                            },
                            options: {
                                "responsive": true,
                                "maintainAspectRatio": false,
                                "tooltips": {
                                    "callbacks": {
                                        "label": function(tooltipItems, data) {
                                            var value = data.datasets[0].data[tooltipItems.index].toLocaleString();
                                            return data.labels[tooltipItems.index] + ': ' + value + ' guests';
                                        }
                                    }
                                }
                            }
                        })
                        </script>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-header header-elements-inline bg-transparent">
                            <h5 class="card-title"><b>Confirmed Bookings</b></h5>
                        </div>
                        <div class="card-body p-3">
                            <h5 class="text-center"><b>{{$confirmed_sales_pax_pie_data['total']}} guests</b></h5>
                            <div style="width: 100%; height: 250px;"><canvas id="confirmed-sales-pax-pie" style="width: 100%; height: 250px;"></canvas></div>
                        </div>
                        <script>
                        var confirmedPaxCtx = document.getElementById('confirmed-sales-pax-pie');
                        var totalConfirmedPaxPie = new Chart(confirmedPaxCtx, {
                            type: 'pie',
                            data: {
                                datasets: [{
                                    "data": [
                                        @json($confirmed_sales_pax_pie_data['data']['Dashboard']),
                                        @json($confirmed_sales_pax_pie_data['data']['Online']),
                                        @json($confirmed_sales_pax_pie_data['data']['ChannelManager']),
                                        @json($confirmed_sales_pax_pie_data['data']['Pending']),
                                    ],
                                    "backgroundColor": [
                                        "#76d4ff", "#ace46b", "#bb6bff", "#ff9900"
                                    ]
                                }],
                                labels: [
                                    'Dashboard',
                                    'Online',
                                    'Channel Manager',
                                    'Pending',
                                ]
                            },
                            options: {
                                "responsive": true,
                                "maintainAspectRatio": false,
                                "tooltips": {
                                    "callbacks": {
                                        "label": function(tooltipItems, data) {
                                            var value = data.datasets[0].data[tooltipItems.index].toLocaleString();
                                            return data.labels[tooltipItems.index] + ': ' + value + ' guests';
                                        }
                                    }
                                }
                            }
                        })
                        </script>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-header header-elements-inline bg-transparent">
                            <div class="d-flex justify-content-between w-100 align-items-center">
                                <h5 class="card-title"><b>Confirmed Bookings - conversion rates</b></h5>
                                <h5 class="mb-0 text-grey"><i class="fa fa-chart-line mr-1"></i> Average: {{$ptcp['overall_average'][0]}}%</h5>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div style="width: 100%; height: 286px;"><canvas id="sales-conversion-pax" style="width: 100%; height: 286px;"></canvas></div>
                        </div>
                        <script>
                        var conversionPaxCtx = document.getElementById('sales-conversion-pax');
                        var conversionRatesPaxPie = new Chart(conversionPaxCtx, {
                            type: 'bar',
                            data: {
                                datasets: [{
                                    label: 'Conversion Rate',
                                    yAxisID: 'y-axis-2',
                                    data: @json($ptcp['average']),
                                    type: "line",
                                    fill: false,
                                    valuesType: 'percentage',
                                    borderColor: "#a4d270",
                                    borderWidth: 2,
                                    backgroundColor: "#a4d270"
                                }, {
                                    label: 'Average',
                                    yAxisID: 'y-axis-2',
                                    data: @json($ptcp['overall_average']),
                                    type: "line",
                                    fill: false,
                                    valuesType: 'percentage',
                                    average:{{$ptcp['overall_average'][0]}},
                                    radius: 0,
                                    borderColor: "#999999",
                                    borderWidth: 2,
                                    backgroundColor: "#999999"
                                }, {
                                    label: 'Total Opportunities',
                                    yAxisID: 'y-axis-1',
                                    data: @json($ptcp['pending']),
                                    backgroundColor: '#dddddd',
                                    valuesType: 'guests',
                                }, {
                                    label: 'Confirmed',
                                    yAxisID: 'y-axis-1',
                                    data: @json($ptcp['confirmed']),
                                    backgroundColor: '#76d4ff',
                                    valuesType: 'guests',
                                }],
                                labels: @json($ptcp['labels'])
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
                                            if (valuesType == 'guests') {
                                                value = value + ' guests';
                                            } else if (valuesType == 'percentage') {
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
                                            callback: function(value) {
                                                if (Number.isInteger(value)) {
                                                    return '€' + value.toLocaleString();
                                                }
                                            },
                                        },
                                        gridLines: {
                                            display: false
                                        }
                                    }, {
                                        type: 'linear',
                                        display: true,
                                        position: 'right',
                                        id: 'y-axis-2',
                                        ticks: {
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

                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-header header-elements-inline bg-transparent">
                            <div class="d-flex justify-content-between w-100 align-items-center">
                                <h5 class="card-title"><b>Confirmed Bookings - breakdown by date</b></h5>
                                <h5 class="mb-0 text-grey"><i class="fa fa-chart-line mr-1"></i> Average: {{round($cbb['average'])}} guests</h5>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div style="width: 100%; height: 286px;"><canvas id="confirmed-sales-breakdown-pax" style="width: 100%; height: 286px;"></canvas></div>
                        </div>
                        <script>
                        var cbbCtx = document.getElementById('confirmed-sales-breakdown-pax');
                        var cbbChart = new Chart(cbbCtx, {
                            type: 'bar',
                            data: {
                                datasets: [{
                                    label: 'Total',
                                    yAxisID: 'y-axis-1',
                                    data: @json($cbb['total']),
                                    type: "line",
                                    fill: false,
                                    valuesType: 'currency',
                                    borderColor: "#a4d270",
                                    borderWidth: 2,
                                    backgroundColor: "#a4d270"
                                }, {
                                    label: 'Average',
                                    yAxisID: 'y-axis-1',
                                    data: @json($cbb['overall_average']),
                                    type: "line",
                                    fill: false,
                                    valuesType: 'currency',
                                    average:{{$cbb['average']}},
                                    radius: 0,
                                    borderColor: "#999999",
                                    borderWidth: 2,
                                    backgroundColor: "#999999"
                                }, {
                                    label: 'Dashboard',
                                    yAxisID: 'y-axis-1',
                                    data: @json($cbb['dashboard']),
                                    backgroundColor: '#76d4ff',
                                    borderColor: '#76d4ff',
                                    borderWidth: 2,
                                    valuesType: 'currency',
                                }, {
                                    label: 'Online',
                                    yAxisID: 'y-axis-1',
                                    data: @json($cbb['online']),
                                    backgroundColor: '#ace46b',
                                    borderColor: '#ace46b',
                                    borderWidth: 2,
                                    valuesType: 'currency',
                                }, {
                                    label: 'Pending',
                                    yAxisID: 'y-axis-1',
                                    data: @json($cbb['pending']),
                                    backgroundColor: '#ff9900',
                                    borderColor: '#ff9900',
                                    borderWidth: 2,
                                    valuesType: 'currency',
                                }, {
                                    label: 'Channel Manager',
                                    yAxisID: 'y-axis-1',
                                    data: @json($cbb['channel']),
                                    backgroundColor: '#bb6bff',
                                    borderColor: '#bb6bff',
                                    borderWidth: 2,
                                    valuesType: 'currency',
                                }],
                                labels: @json($csb['labels'])
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
                                            if (valuesType == 'currency') {
                                                value = value + ' guests';
                                            } else if (valuesType == 'percentage') {
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
                                            beginAtZero: true
                                        }
                                    }],
                                }
                            },
                        })
                        </script>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-header header-elements-inline bg-transparent">
                            <h5 class="card-title"><b>Confirmed Bookings - breakdown by location</b></h5>
                        </div>
                        <div class="card-body p-3">
                            <div style="width: 100%; height: 286px;"><canvas id="confirmed-bookings-pax-location" style="width: 100%; height: 286px;"></canvas></div>
                        </div>
                        <script>
                        var cblCtx = document.getElementById('confirmed-bookings-pax-location');
                        var cblChart = new Chart(cblCtx, {
                            type: 'line',
                            data: {
                                datasets: [
                                    @foreach ($selected_camps as $index => $camp)
                                    {
                                        label: '{{$camp->short_name}}',
                                        fill: false,
                                        yAxisID: 'y-axis-1',
                                        data: @json($cbl[($index + 1)]),
                                        backgroundColor: '#{{$camp->color}}',
                                        borderColor: '#{{$camp->color}}',
                                        borderWidth: 2,
                                        valuesType: 'guests',
                                    },
                                    @endforeach
                                ],
                                labels: @json($cbl['labels'])
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
                                            if (valuesType == 'guests') {
                                                value = value + ' guests';
                                            } else if (valuesType == 'percentage') {
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
                                            beginAtZero: true
                                        }
                                    }],
                                }
                            },
                        })
                        </script>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

@section('scripts')
    <script>
    tippy('.tippy', {
        content: 'Tooltip',
        arrow: true,
    });
    $('.multiselect').multiselect({
        nonSelectedText: 'Select Camp',
        includeSelectAllOption: true,
    });
    $('.daterange-empty').daterangepicker({
        autoApply: true,
        showDropdowns: true,
        minDate: "01/01/2022",
        minYear: 2022,
        maxYear: 2030,
        locale: {
            format: 'DD.MM.YYYY'
        }
    });
    </script>
@endsection
