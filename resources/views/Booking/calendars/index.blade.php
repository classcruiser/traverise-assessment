@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper">
            <div class="content">
                <div class="d-flex justify-content-between align-items-center my-3">
                    <h3 class="mb-0">{{$location->name}} Calendar</h3>

                    <form action="{{route('tenant.calendar.show', [ 'id' => $id ])}}" method="get">
                        <div class="select-period d-flex justify-content-end align-items-center">
                            <span class="mr-2">Show</span>
                            <div style="width: 100px">
                                <select name="start_date_y" class="form-control">
                                    @for($i = 2022; $i <= 2027; $i++)
                                        <option value="{{$i}}" {{$start_date_y == $i ? 'selected' : ''}}>{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div style="width: 100px" class="ml-1">
                                <select name="start_date_m" class="form-control">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{$i}}" {{$start_date_m == $i ? 'selected' : ''}}>{{date('F', strtotime(date('Y') .'-'. $i .'-01'))}}</option>
                                    @endfor
                                </select>
                            </div>
                            <span class="mx-2"><i class="fal fa-arrow-right"></i></span>
                            <div style="width: 100px">
                                <select name="end_date_y" class="form-control">
                                    @for($i = 2022; $i <= 2027; $i++)
                                        <option value="{{$i}}" {{$end_date_y == $i ? 'selected' : ''}}>{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div style="width: 100px" class="ml-1">
                                <select name="end_date_m" class="form-control">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{$i}}" {{$end_date_m == $i ? 'selected' : ''}}>{{date('F', strtotime(date('Y') .'-'. $i .'-01'))}}</option>
                                    @endfor
                                </select>
                            </div>
                            {!! csrf_field() !!}
                            <button class="btn btn-labeled btn-labeled-left bg-danger ml-1 btn-sm" type="submit">
                                <b><i class="icon-loop3"></i></b> Show Calendar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="row">

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="calendar-wrap">
                                    <table class="calendar">
                                        <thead>
                                            <tr>
                                                <th class="th-label top" rowspan="2"></th>
                                                @foreach($calendar['days'] as $month => $days)
                                                    @foreach($days as $day)
                                                        @if($loop->first)<th class="th-date th-month" colspan="{{count($days)}}"><span>{{$month}}</span></th>@endif
                                                        @endforeach
                                                    @endforeach
                                            </tr>
                                            <tr>
                                                @foreach($calendar['days'] as $month => $days)
                                                    @foreach($days as $day)<th class="th-date {{$loop->first ? 'th-date-first' : ''}} {{date('D', strtotime($day))}} {{date('Y-m-d') == date('Y-m-d', strtotime($day)) ? 'today' : ''}}">{{date('d', strtotime($day))}}</th>@endforeach
                                                    @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rooms as $room)
                                                <tr class="td-bikes">
                                                    <td colspan="{{$calendar['length'] + 1}}"><a href="javascript:" class="room-toggle" data-id="{{$room->id}}" data-status="open"><span><i class="fa fa-fw fa-caret-down"></i></span>{{$room->name}}</a></td>
                                                </tr>
                                                @foreach($room->rooms as $r)
                                                    <?php $capacity = $r->beds > 500 ? 500 : $r->beds; ?>
                                                    @for($i = 1; $i <= $capacity; $i++)
                                                        <tr class="tr-date room-{{$room->id}} bed-{{$i}} date-available">
                                                            <td>
                                                                <div class="room-label">
                                                                    {{$i == 1 ? (Auth::user()->role_id == 4 ? $r->agent_name : $r->name) : ''}}
                                                                    @if($i == 1)
                                                                        <span>{{$capacity}}x <i class="far fa-bed"></i></span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            @foreach($calendar['days'] as $month => $days)
                                                                @foreach($days as $day)
                                                                    <td class="td-date" data-id="{{$day .'_'. $location_id .'_'. $room->id .'_'. $r->id .'_'. $i}}">
                                                                        <div class="cell-container">
                                                                            @if (isset($pricing_calendars[$room->id]) && isset($pricing_calendars[$room->id][$day]))
                                                                                <div class="cell-block"><i class="far fa-times fa-fw"></i></div>
                                                                            @else
                                                                                @if (array_key_exists($r->id, $cals) && array_key_exists($i, $cals[$r->id]) && array_key_exists($day, $cals[$r->id][$i]))
                                                                                    @php
                                                                                    $cell = $cals[$r->id][$i][$day];
                                                                                    if (!array_key_exists($month .'-'. $day, $total_inhouse)) {
                                                                                        $total_inhouse[$month .'-'. $day] = 1;
                                                                                    }
                                                                                    @endphp
                                                                                    @if($cals[$r->id][$i][$cell['id']]['first'])
                                                                                        <div href="javascript:" data-id="{{$cell['id']}}" data-guest-id="{{$cell['guest']}}" data-ref="{{$cell['ref']}}" data-tippy-content="<strong>{{$cell['ref']}}</strong><br />{{$cell['assigned_guests'] != '' ? $cell['assigned_guests'] : 'HIDDEN'}}<br />{{$cell['booking_state'] != 'DRAFT' ? '<br />Status: '. $cell['booking_status'] .'<br />State: '. $cell['state'] . $cell['private_booking'] : ''}}" class="cal-cell td-link td-popup {{!$cell['is_first'] ? 'cell-prevmonth' : ''}} {{$cell['is_external'] ? 'cell-external' : ''}} {{ $cell['is_agent'] ? 'bg-agent' : '' }} status-{{$cell['booking_state']}} {{ $cell['booking_state'] }}" style="height: {{intVal(33 * $cell['cell_height'])}}px" data-cell-count="{{$cell['nights']}}" data-nights-offset="{{$cell['nights_offset']}}" data-is-first="{{!$cell['is_first'] ? 'prev' : ''}}">
                                                                                            <span>
                                                                                                <i class="fa fa-fw fa-user {{$cell['gender']}}"></i>
                                                                                                {{$cell['ref']}} - {!! $cell['assigned_guests'] !!} {!! $cell['is_private'] ? ' <i class="fa fa-fw fa-lock"></i>' : '' !!}
                                                                                            </span>
                                                                                            <a href="{{route('tenant.bookings.show', [ 'ref' => $cell['ref'] ])}}" target="_blank" class="cell-link">
                                                                                                <i class="fa fa-arrow-up-right-from-square fa-fw"></i>
                                                                                            </a>
                                                                                        </div>
                                                                                        @php
                                                                                            $cals[$r->id][$i][$cell['id']]['first'] = 0;
                                                                                        @endphp
                                                                                    @endif
                                                                                    <?php $total_inhouse[$month .'-'. $day] += 1; ?>
                                                                                @else
                                                                                    <a href="javascript:" class="td-link">&nbsp;</a>
                                                                                @endif
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                @endforeach
                                                            @endforeach
                                                        </tr>
                                                    @endfor
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                        <thead>
                                            <tr>
                                                <th class="th-label top text-center" rowspan="2">TOTAL IN HOUSE</th>
                                                @foreach($calendar['days'] as $month => $days)
                                                    @foreach($days as $day)
                                                        @if($loop->first)<th class="th-date th-month" colspan="{{count($days)}}"><span>{{$month}}</span></th>@endif
                                                        @endforeach
                                                    @endforeach
                                            </tr>
                                            <tr>
                                                @foreach($calendar['days'] as $month => $days)
                                                    @foreach($days as $day)
                                                        <th class="bottom th-total">{{array_key_exists($month .'-'. $day, $total_inhouse) ? $total_inhouse[$month .'-'. $day] : 0}}</th>
                                                    @endforeach
                                                @endforeach
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('subscripts')
    <script>
    var totalCells = {{intVal($calendar['length'])}};

    $('.room-toggle').click(function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        let status = $(this).data('status');
        let newStatus = status == 'open' ? 'close' : 'open';
        let newIcon = status == 'open' ? '<i class="fa fa-fw fa-caret-right"></i>' : '<i class="fa fa-fw fa-caret-down"></i>';
        $('.room-' + id).toggle();
        $(this).data('status', newStatus);
        $(this).find('span').html(newIcon);
    });

    function resizeCalendar() {
        var $el = $('table.calendar');
        var totalWidth = (totalCells * 38) + 300 + 200;
        $el.css({
            width: totalWidth + 'px'
        });

        var $popup = $('.td-popup');
        var cellWidth = $('.td-date').outerWidth() + 1;
        $popup.each(function(el) {
            var width = parseInt($(this).data('nights-offset')) * cellWidth;
            var isPrev = $(this).data('is-first') == 'prev';
            width = isPrev ? width + (cellWidth / 2) - 4 : width - 2;
            $(this).css({
                width: width + 'px'
            });
        })
    }

    function styleTransferCell() {
        var refs = [];
        var $el = $('.td-popup');

        $el.each(function(el) {
            var ref = $(this).data('ref');
            var index = refs.indexOf(ref);

            if (index <= -1) {
                refs.push(ref);
            }
        });

        if (refs.length > 0) {
            refs.map(ref => {
                var $check = $('.td-popup[data-ref=' + ref + ']');
                if ($check.length > 1) {
                    $check.addClass('td-transfer');
                }
            })
        }
    }

    styleTransferCell();

    $(document).ready(function() {
        resizeCalendar();
        tippy('.td-popup', {
            content: 'Tooltip',
            arrow: true,
            delay: 0,
            duration: 0,
            followCursor: true,
            allowHTML: true,
        })

        $('a.cal-cell').on('click', function (e) {
            const url = $(this).data('url');

            if (url) {
                window.open(url, '_blank');
            }
        })

        @if (auth()->user()->can_drag)
            var containers = $('.cell-container').toArray();
            var drake = dragula(containers, {
            isContainer: function(el) {
                return el.classList.contains('cell-container');
            },
            moves: function(el, source, handle, sibling) {
                return true;
            },
            accepts: function(el, target, source, sibling) {
                return true;
            },
            invalid: function(el, handle) {
                return false;
            },
            copy: false,
            reventOnSpill: true,
            mirrorContainer: document.querySelector('.cell-container')
        });
        drake.on('drag', function(el, source) {
            $('.cell-container').css({
                "pointer-events": "auto"
            })
        });
        drake.on('dragend', function(el) {
            $('.cell-container').css({
                "pointer-events": "none"
            })
        });
        drake.on('drop', function(el, target, source, sibling) {
            if (!window.confirm('Are you sure you want to move this booking?')) {
                drake.cancel(true);
                return false;
            }
            var bookingRoomID = $(el).data('id');
            var guestId = $(el).data('guest-id')
            var nights = ($(el).data('cell-count'));
            var targetID = $(target).parent().data('id');

            if (bookingRoomID && targetID) {
                // call AXIOS to update the position
                const data = {
                    bookingRoomID,
                    target: targetID,
                    nights,
                    guestId
                };

                axios
                    .post('/bookings/quick-move', data)
                    .then(res => {
                        return false;
                    })
                }
            })
        @endif
    })
    </script>
@endsection
