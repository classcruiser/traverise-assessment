<div class="page-content">
    <div class="content-wrapper">
        <div class="content">

            <form action="/reports/daily" method="get">
                <div class="d-flex justify-content-between align-items-center my-3">
                    <h3 class="mb-0">Daily Overview</h3>
                    <span class="ml-auto">CAMP</span>
                    <div style="width: 220px;" class="ml-2 mr-1">
                        <select class="form-control multiselect" name="camps[]" multiple="multiple" data-fouc>
                            @foreach($locations as $camp)
                                <option value="{{$camp->id}}" {{!request()->has('camps') || in_array($camp->id, request('camps')) ? 'selected' : ''}}>{{$camp->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="select-period d-flex justify-content-end align-items-center">
                        <div style="width: 70px">
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
                </div>
            </form>

            <div class="row">
                <!-- LEFT -->
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="overflow-x">
                                <table class="table table-xs table-report-custom table-striped">
                                    <thead>
                                        <tr class="bg-grey-700">
                                            <th>Date</th>
                                            <th class="text-center">Booking</th>
                                            <th class="text-center">Abandoned</th>
                                            <th class="text-center">Pending</th>
                                            <th class="text-center">Pending Conf.</th>
                                            <th class="text-center">Sale</th>
                                            <th class="text-center">Manual Conf.</th>
                                            <th class="text-center">Total Conf.</th>
                                            <th class="text-center">Total AVG</th>
                                            <th class="text-center">Income</th>
                                            @foreach($cols as $col)
                                                <th class="text-center">{{$col['name']}}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dates as $date)
                                            <tr>
                                                <td width="10%"><b>{{$date['label']}}</b></td>
                                                <td class="text-center" width="120">
                                                    @if($date['booking'] > 0)
                                                        <a href="/bookings?ref=&booking_date_from= {{$date['label']}}&booking_date_to= {{$date['label']}}&{{$camps_url}}&_token" title="" class="text-danger"><b>{{$date['booking']}}</b></a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center col-abandoned" width="120">
                                                    @if($date['abandoned'] > 0)
                                                        <a href="/bookings?ref=&booking_date_from= {{$date['label']}}&booking_date_to= {{$date['label']}}&status[]=ABANDONED&{{$camps_url}}&_token" title="" class="text-danger"><b>{{$date['abandoned']}}</b></a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center col-pending" width="120">
                                                    @if($date['pending'] > 0)
                                                        <a href="/bookings?ref=&booking_date_from= {{$date['label']}}&booking_date_to= {{$date['label']}}&opportunity=Pending&status[]=PENDING&{{$camps_url}}&_token" title="" class="text-danger"><b>{{$date['pending']}}</b></a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center col-manual" width="130">
                                                    @if($date['pending_confirm'] > 0)
                                                        <a href="/bookings?ref=&booking_date_from= {{$date['label']}}&booking_date_to= {{$date['label']}}&opportunity=Pending&status[]=CONFIRMED&{{$camps_url}}" title="" class="text-danger"><b>{{$date['pending_confirm']}}</b></a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center col-sale" width="120">
                                                    @if($date['sale'] > 0)
                                                        <a href="/bookings?ref=&booking_date_from= {{$date['label']}}&booking_date_to= {{$date['label']}}&channelExclude=Dashboard&opportunity=Sale&status[]=CONFIRMED&status[]=DRAFT&{{$camps_url}}&_token" title="" class="text-danger"><b>{{$date['sale']}}</b></a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center col-manual" width="120">
                                                    @if($date['manual_confirm'] > 0)
                                                        <a href="/bookings?ref=&booking_date_from= {{$date['label']}}&booking_date_to= {{$date['label']}}&channel=Dashboard&status[]=CONFIRMED&{{$camps_url}}&_token" title="" class="text-danger"><b>{{$date['manual_confirm']}}</b></a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center col-total" width="120">{!! $date['total'] > 0 ? $date['total'] : '<span class="text-muted">-</span>' !!}</td>
                                                <td class="text-center" width="120">{{$date['avg']}}</td>
                                                <td class="text-center" width="120"><b><a href="/reports?dates= {{$date['label']}}+-+{{$date['label']}}" title="" class="text-danger">&euro;{{number_format($date['income'], 2)}}</a></b></td>
                                                @foreach($cols as $col)
                                                    <td class="text-center" width="85">{!! $date['guests_count'][$col['id']] > 0 ? $date['guests_count'][$col['id']] : '<span class="text-muted">-</span>' !!}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td><b>TOTAL</b></td>
                                            <td class="text-center"><b>{{$total['total_bookings']}}</b></td>
                                            <td class="text-center col-abandoned"><b>{{$total['total_abandoned']}}</b></td>
                                            <td class="text-center col-pending"><b>{{$total['total_pendings']}}</b></td>
                                            <td class="text-center col-manual"><b>{{$total['total_pending_confirm']}}</b></td>
                                            <td class="text-center col-sale"><b>{{$total['total_sales']}}</b></td>
                                            <td class="text-center col-manual"><b>{{$total['total_manual_confirm']}}</b></td>
                                            <td class="text-center col-total"><b>{{$total['total_confirmed']}}</b></td>
                                            <td class="text-center"><b>{{$total['total_avgs']}}</b></td>
                                            <td class="text-center"><b>&euro;{{number_format($total['total_income'], 2)}}</b></td>
                                            @foreach($cols as $col)
                                                <td class="text-center"><b>{{$total['total_camp_'. $col['id']]}}</b></td>
                                            @endforeach
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<style type="text/css">
.table-report-custom tr th:first-child {
    background-color: #555;
    position: sticky;
    left: 0;
}

.table-report-custom tr td:first-child {
    background-color: #fff;
    position: sticky;
    left: 0;
}
</style>
@section('scripts')
    <script>
    $('.multiselect').multiselect({
        nonSelectedText: 'Select Camp',
        includeSelectAllOption: true,
    });

    $(function() {
        const tableWidth = {{$table_width}};
        if (window.innerWidth < tableWidth) {
            $('.table.table-report-custom').width({{$table_width}});
        }
    })
    </script>
@endsection