<div class="sidebar sidebar-component sidebar-booking sidebar-main sidebar-expand-md align-self-start ml-3 new">
    <div class="sidebar-content">
        <div class="card bg-white new">
            <div class="card-header new header-elements-inline">
                <h6 class="card-title new">Details</h6>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-2 pb-0 px-3 sidebar-body">
                <label class="text-muted pb-0 mb-0">Location</label>
                <div class="text-bold mb-2"><b>{{$booking->location_id ? $booking->location->name : '--'}}</b></div>

                <label class="text-muted pb-0 mb-0">Stay</label>
                <div class="text-bold mb-2"><b>{{$booking->check_in ? $booking->check_in->format('d.m.Y') .' - '. $booking->check_out->format('d.m.Y') : '--'}}</b></div>

                <label class="text-muted pb-0 mb-0">Guests</label>
                <div class="text-bold mb-2"><b>{{$booking->guests_count > 0 ? $booking->guests_count .' guests' : '--'}}</b></div>

                <label class="text-muted pb-0 mb-1">Agent</label>
                <div class="text-bold {{$booking->status == 'CONFIRMED' ? 'mb-3' : 'mb-2'}}">
                    <select class="form-control select-agent" data-placeholder="Agent" data-ref="{{$booking->ref}}" {{$booking->status == 'CANCELLED' || $is_deleted ? 'disabled' : ''}}>
                        <option value="">No Agent</option>
                        @foreach($agents as $agent)
                            <option value="{{$agent->id}}" {{$booking->agent_id == $agent->id ? 'selected' : ''}}>{{$agent->name}}</option>
                        @endforeach
                    </select>
                </div>

                @if($booking->status != 'CONFIRMED' && !$is_deleted)
                    <label class="text-muted pb-0 mb-0">Expires after</label>
                    <div class="mt-1 mb-3 d-flex justify-content-start align-items-center">
                        <div style="width: 90px" class="mr-2">
                            <select class="form-control select-no-search expire-date" data-fouc data-placeholder="Expiration date" data-ref="{{$booking->ref}}">
                                <option value="12" {{$booking->expire_at == 12 ? 'selected' : ''}}>12H</option>
                                <option value="24" {{$booking->expire_at == 24 ? 'selected' : ''}}>24H</option>
                                <option value="36" {{$booking->expire_at == 36 ? 'selected' : ''}}>36H</option>
                                <option value="99" {{$booking->expire_at == 99 ? 'selected' : ''}}>&infin;</option>
                            </select>
                        </div>
                        <div>
                            at <b class="expired-at">{{$booking->expiry->format('d.m.Y H:i')}}</b>
                        </div>
                    </div>
                @else
                    @if($booking->payment == 'DUE')
                        <label class="text-muted pb-0 mb-0">Due date</label>
                        <div class="mt-1 mb-3 d-flex justify-content-start align-items-center">
                            <div class="input-group input-group-sm">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                </span>
                                <input type="text" class="form-control date-basic due-date" data-ref="{{$booking->ref}}" value="{{$booking->payment->deposit_due_date->format('d.m.Y')}}">
                            </div>
                        </div>
                    @endif
                @endif

                <label class="text-muted pb-0 mb-0">Tax Visibility</label>
                    <div class="mt-1 mb-3 d-flex justify-content-start align-items-center">
                        <div style="width: 90px" class="mr-2">
                            <select class="form-control select-no-search tax-visibility" data-fouc data-placeholder="Tax visibility" data-ref="{{$booking->ref}}">
                                <option value="1" {{ $booking->tax_visible ? 'selected' : '' }}>ON</option>
                                <option value="0" {{ !$booking->tax_visible ? 'selected' : '' }}>OFF</option>
                            </select>
                        </div>
                    </div>

                @if($booking->notes && $booking->notes != '')
                    <label class="text-muted pb-0 mb-0">REQUEST / COMMENT <i class="fad text-info fa-exclamation-circle"></i></label>
                    <div class="mt-1 mb-3 d-flex justify-content-start align-items-center">
                        {{$booking->notes}}
                    </div>
                @endif

            </div>
        </div>

        <div class="card bg-white new">
            <div class="card-header new header-elements-inline">
                <h6 class="card-title new">Main Guest {!! (!$booking->guest) ? '<span><i class="far fa-exclamation-triangle"></i></span>' : '<span><i class="far fa-check is-green"></i></span>' !!}</h6>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                    </div>
                </div>
            </div>

            <div class="card-body pt-2 pb-0 px-3 position-relative">
                @if($booking->guest && !$is_deleted)
                    @canany(['edit guest', 'add guest room', 'delete guest'])
                        <div class="btn-group position-absolute top-0 right-0 pt-2 pr-2">
                            <a href="#" class="text-slate" data-toggle="dropdown"><i class="icon-cog4"></i></a>
                            <div class="dropdown-menu dropdown-menu-sm">
                                <a href="/bookings/{{$booking->ref}}/edit-guest/{{$booking->guest->id}}" class="dropdown-item"><i class="fa fa-fw fa-user mr-2"></i> Edit guest</a>
                                @if(!$booking->archived)
                                    <a href="/bookings/{{$booking->ref}}/guest/{{$booking->guest->id}}/new-room" class="dropdown-item"><i class="fa fa-fw fa-bed mr-2"></i> New room</a>
                                @endif
                                <a href="/bookings/{{$booking->ref}}/guest/{{$booking->guest->id}}/remove" class="dropdown-item confirm-dialog"><i class="fa fa-fw fa-times mr-2"></i> Remove guest</a>
                            </div>
                        </div>
                    @endcanany
                @endif
                <label class="text-muted pb-0 mb-0">Full Name</label>
                <div class="text-bold mb-2">
                    <b>{{$booking->guest ? $booking->guest->details->full_name : '--'}}</b>
                </div>

                @if ($booking->guest && $booking->guest->details->client_number)
                    <label class="text-muted pb-0 mb-0">Client ID</label>
                    <div class="text-bold mb-2">
                        <b>{{ $booking->guest->details->client_number ?? '--' }}</b>
                    </div>
                @endif

                <label class="text-muted pb-0 mb-0">Email</label>
                <div class="text-bold mb-2"><b>{{$booking->guest ? $booking->guest->details->email : '--'}}</b></div>

                @if($booking->guest && $booking->guest->details->phone != '' && $booking->guest->details->phone != '---')
                    <label class="text-muted pb-0 mb-0">Phone</label>
                    <div class="text-bold mb-2"><b>{{$booking->guest->details->mobphonephonele}}</b></div>
                @endif

                <div class="text-muted pb-0 mb-0">Room</div>
                @if($booking->guest)
                    @if($booking->guest->rooms)
                        <ul class="list-group border-0 p-0">
                            @foreach($booking->guest->rooms as $room_info)
                                <li class="list-group-item p-0 mb-2">
                                    <div class="flex-fill">
                                        <div class="d-flex justify-content-between">
                                            <span class="mb-1"><i class="fa fa-bed mr-1"></i> <b>{{$role == 4 ? $room_info->room->subroom->agent_name : $room_info->room->room->name .': '. $room_info->room->subroom->name}}</b> {!! $room_info->room->is_private ? '<i class="fa fa-fw fa-lock tippy font-size-xs" data-tippy-content="Private Booking"></i>' : '' !!}</span>
                                            <span class="text-muted"></span>
                                            @if(!$is_deleted)
                                                @can('edit guest room')
                                                    <a href="/bookings/{{$booking->ref}}/guest/{{$booking->guest->id}}/rooms/{{$room_info->booking_room_id}}" title="" class="ml-auto font-size-sm text-slate">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    @can('delete guest room')
                                                        <a href="javascript:" title="" class="ml-1 text-danger delete-booking-room" data-room-id="{{$room_info->booking_room_id}}">
                                                            <i class="icon-cross2"></i>
                                                        </a>
                                                    @endcan
                                                @endcan
                                            @endif
                                        </div>
                                        <span>
                                            {{date('d.m.Y', strtotime($room_info->room->from))}}
                                            <i class="icon-arrow-right5 mx-1 font-size-sm"></i>
                                            {{date('d.m.Y', strtotime($room_info->room->to))}}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @else
                    <div class="text-bold mb-2"><b>No room assigned. Add it first.</b></div>
                @endif
            </div>
        </div>

        @if($booking->other_guests->count())
            <div class="card bg-white new">
                <div class="card-header new header-elements-inline">
                    <h6 class="card-title new">Other Guests</h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 position-relative">

                    @if($booking->other_guests->count())
                        @foreach($booking->other_guests as $og)
                            <div class="py-2 px-3 {{!$loop->first ? 'border-top-1 border-alpha-grey' : ''}} position-relative">
                                <label class="text-muted pb-0 mb-0 d-flex justify-content-between align-items-center">
                                    Guest {{$loop->index + 1}}
                                    @if(!$is_deleted)
                                        @canany(['edit guest', 'add guest room', 'delete guest'])
                                            <div class="btn-group position-absolute top-0 right-0 pt-2 pr-2">
                                                <a href="#" class="text-slate" data-toggle="dropdown"><i class="icon-cog4"></i></a>
                                                <div class="dropdown-menu dropdown-menu-sm">
                                                    <a href="/bookings/{{$booking->ref}}/edit-guest/{{$og->id}}" class="dropdown-item"><i class="fa fa-fw fa-user mr-2"></i> Edit guest</a>
                                                    @if(!$booking->archived)
                                                        <a href="/bookings/{{$booking->ref}}/guest/{{$og->id}}/new-room" class="dropdown-item"><i class="fa fa-fw fa-bed mr-2"></i> New room</a>
                                                    @endif
                                                    <a href="/bookings/{{$booking->ref}}/guest/{{$og->id}}/remove" class="dropdown-item confirm-dialog"><i class="fa fa-fw fa-times mr-2"></i> Remove guest</a>
                                                </div>
                                            </div>
                                        @endcanany
                                    @endif
                                </label>
                                <div class="text-bold mb-2">
                                    <b>{{$og->details->full_name}}</b>
                                </div>

                                @if ($og->details->client_number)
                                    <div class="text-muted pb-0 mb-0">Client ID</div>
                                    <div class="mb-2 font-weight-bold">{{ $og->details->client_number ?? '--' }}</div>
                                @endif

                                <div class="text-muted pb-0 mb-0">Email</div>
                                <div class="mb-2 font-weight-bold">{!! $og->details ? $og->details->email : '--' !!}</div>

                                @if($og->rooms->count())
                                    <div class="text-muted pb-0 mb-0">Room</div>
                                    <ul class="list-group border-0 p-0">
                                        @foreach($og->rooms as $room_info)
                                            <li class="list-group-item p-0 mb-2">
                                                <div class="flex-fill">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="mb-1"><i class="fa fa-bed mr-1"></i> <b>{{$role == 4 ? $room_info->room->subroom->agent_name : $room_info->room->room->name .': '. $room_info->room->subroom->name}}</b> {!! $room_info->room->is_private ? '<i class="fa fa-fw fa-lock tippy font-size-xs" data-tippy-content="Private Booking"></i>' : '' !!}</span>
                                                        <span class="text-muted"></span>

                                                        @if(!$is_deleted)
                                                            @can('edit guest room')
                                                                <a href="/bookings/{{$booking->ref}}/guest/{{$og->id}}/rooms/{{$room_info->booking_room_id}}" title="" class="ml-auto text-slate"><i class="fa fa-edit"></i></a>
                                                            @endcan

                                                            @can('delete guest room')
                                                                <a href="javascript:" title="" class="ml-1 text-danger delete-booking-room" data-room-id="{{$room_info->booking_room_id}}"><i class="icon-cross2"></i></a>
                                                            @endcan
                                                        @endif
                                                    </div>
                                                    <span>{{date('d.m.Y', strtotime($room_info->room->from))}} <i class="icon-arrow-right5 mx-1 font-size-sm"></i> {{date('d.m.Y', strtotime($room_info->room->to))}}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <a href="/bookings/{{$booking->ref}}/guest/{{$og->id}}/new-room" title="" class="btn btn-sm bg-slate mb-2 py-0 d-block rounded-round">
                                        <i class="fa fa-bed mr-1"></i> Add room
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endif

        @can('download PDF')
            @if ($booking->histories->where('action', 'Generate Invoice (automatic)')->count())
                <div class="card bg-white new">
                    <div class="card-header new header-elements-inline">
                        <h6 class="card-title new">Invoice Snapshots</h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-2 pb-0 px-3 position-relative">
                        @foreach($booking->histories as $history)
                            @if($history->action === 'Generate Invoice (automatic)')
                                <div class="mb-2">
                                    <a href="{{ route('tenant.bookings.downloadInvoiceSnapshot', ['ref' => $booking->ref, 'history_id' => $history->id]) }}">
                                        <i class="fa fa-fw fa-file-pdf text-danger"></i>
                                        <span class="text-muted">{{$history->created_at->format('M d, H:i:s')}}</span>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endcan

        @if ($booking->status == 'CANCELLED')
            <div class="card bg-white new">
                <div class="card-header new header-elements-inline">
                    <h6 class="card-title new">Archive</h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-2 pb-0 px-3 position-relative">
                    <div class="mb-2">
                        <a href="/bookings/{{$booking->ref}}/pdf-invoice" class="">
                            <i class="fa fa-fw fa-file-pdf text-danger"></i>
                            <span class="text-muted">Initial Invoice</span>
                        </a>
                        <br />
                        <a href="/bookings/{{$booking->ref}}/pdf-invoice?CANCELLATION" class="">
                            <i class="fa fa-fw fa-file-pdf text-danger"></i>
                            <span class="text-muted">Invoice Correction</span>
                        </a>
                        @if ($booking->cancellation)
                            <br />
                            <a href="/bookings/{{$booking->ref}}/cancellation-invoice/{{ $booking->cancellation->id }}">
                                <i class="fa fa-fw fa-file-pdf text-danger"></i>
                                <span class="text-muted">Invoice of Cancellation</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>

@section('subscripts')
    <script>
    $('.date-basic').daterangepicker({
        autoApply: true,
        singleDatePicker: true,
        locale: {
            format: 'DD.MM.YYYY'
        }
    });
    </script>
@endsection
