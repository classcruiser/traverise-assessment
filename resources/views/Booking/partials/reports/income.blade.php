<div class="page-content container">
  <div class="content-wrapper">
    <div class="content">

      <form action="/reports/income" method="get">
        <div class="d-flex justify-content-start align-items-center my-3">
          <h3 class="mb-0">Income Report</h3>
          <div style="width: 220px;" class="ml-auto">
            <select class="form-control multiselect" name="camps[]" multiple="multiple" data-fouc>
              @foreach ($locations as $camp)
                <option value="{{ $camp->id }}" {{ !request()->has('camps') || in_array($camp->id, request('camps')) ? 'selected' : '' }}>{{ $camp->name }}</option>
              @endforeach
            </select>
          </div>
          <div style="width: 220px" class="ml-1">
            <div class="input-group">
              <span class="input-group-prepend">
                <span class="input-group-text"><i class="icon-calendar22"></i></span>
              </span>
              <input type="text" class="form-control date-range" name="dates" value="{{ $dates['default'] }}" placeholder="select dates" />
            </div>
          </div>
          @csrf
          <button class="btn btn-labeled btn-labeled-left bg-danger ml-1 btn-sm">
            <b><i class="icon-reset"></i></b> Update
          </button>
        </div>
      </form>

      <div class="row">

        <div class="col-sm-4">
          <div class="card">
            <div class="card-body p-3">
              <h5 class="text-center"><b>&euro;{{ number_format($total_income) }}</b></h5>
              <div style="max-height: 250px;"><canvas id="pie-chart" style="width: 100%; height: 250px;"></canvas></div>
            </div>
            <script>
            var oppCtx = document.getElementById('pie-chart');
            var totalOppPie = new Chart(oppCtx, {
              type: 'pie',
              data: {
                datasets: [
                  {
                  "data": [
                    @foreach ($selected_camps as $index => $camp)
                      @json($pie_data['data'][$camp->id]),
                    @endforeach
                  ],
                  "backgroundColor": [
                    @foreach ($selected_camps as $index => $camp)
                      '#{{ $camp->color }}',
                    @endforeach
                  ]
                }],
                labels: [
                  @foreach ($selected_camps as $index => $camp)
                    '{{ $camp->short_name }}',
                  @endforeach
                ]
              },
              options: {
                "responsive": true,
                "maintainAspectRatio":false,
                "tooltips": {
                  "callbacks": {
                    "label": function(tooltipItems, data) {
                      var value = data.datasets[0].data[tooltipItems.index].toLocaleString();
                      return data.labels[tooltipItems.index] +': €' + value;
                    }
                  }
                }
              }
            })
            </script>
          </div>
        </div>

        <div class="col-sm-8">
          <div class="card">
            <div class="card-body p-3" style="max-height: 320px;">
              <canvas id="income-chart" style="width: 100%; height: 286px;"></canvas>
            </div>
            <script>
            var cflCtx = document.getElementById('income-chart');
            var cflChart = new Chart(cflCtx, {
              type: 'line',
              data: {
                datasets: [
                  @foreach ($selected_camps as $index => $camp)
                  {
                    label: '{{ $camp->short_name }}',
                    fill: false,
                    yAxisID: 'y-axis-1',
                    data: @json($income_chart[($index + 1)]),
                    backgroundColor: '#{{ $camp->color }}',
                    borderColor: '#{{ $camp->color }}',
                    borderWidth: 2,
                    valuesType: 'currency',
                  },
                  @endforeach
                ],
                labels: @json($income_chart['labels'])
              },
              options: {
                responsive: true,
                maintainAspectRatio:false,
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
                      callback: function (value) {
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

        <div class="col-sm-12">
          <div class="card">
            <div class="card-header header-elements-inline bg-transparent">
              <h5 class="card-title"><b>{{ $dates['default'] }}</b></h5>
            </div>
            <table class="table table-xs table-compact datatable-basic" data-page-length="25">
              <thead>
                <tr class="bg-grey-700">
                  <th>REF</th>
                  <th>GUEST</th>
                  <th>CHECK IN</th>
                  <th>CHECK OUT</th>
                  <th>Location</th>
                  <th class="text-right">INCOME</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($bookings as $booking)
                  <tr>
                    <td><a href="/bookings/{{ $booking->ref }}" title="" class="text-danger font-weight-bold">{{ $booking->ref }}</a></td>
                    <td><a href="{{ isset($booking->guest->details) ? '/guests/'. $booking->guest->details->id : '#' }}" title="" class="text-danger font-weight-bold">{{ isset($booking->guest->details) ? $booking->guest->details->full_name : '---' }}</a></td>
                    <td>{{ $booking->check_in->format('d.m.Y') }}</td>
                    <td>{{ $booking->check_out->format('d.m.Y') }}</td>
                    <td>{{ $booking->location->short_name }}</td>
                    <td class="text-right" data-order="{{ round($booking->income) }}"><b>&euro;{{ $booking->parsePrice($booking->income) }}</b></td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6">No result</td>
                  </tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="5" class="text-right"><b>TOTAL</b></td>
                  <td class="text-right"><b>&euro;{{ number_format($total_income, 2) }}</b></td>
              </tfoot>
            </table>
          </div>
        </div>

      </div>

    </div>

  </div>
</div>

@section('scripts')
<script>
$('.multiselect').multiselect({
  nonSelectedText: 'Select Camp',
  includeSelectAllOption: true,
});
// Basic datatable

</script>
@endsection
