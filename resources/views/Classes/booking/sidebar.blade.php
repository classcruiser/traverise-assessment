<div class="sidebar sidebar-booking sidebar-main sidebar-expand-md align-self-start ml-3 new">
    <div class="sidebar-content">

        <div class="card bg-white new">
            <div class="card-header new header-elements-inline">
                <h6 class="card-title new">Booker {!! (!$booking->guest) ? '<span><i class="far fa-exclamation-triangle"></i></span>' : '<span><i class="far fa-check is-green"></i></span>' !!}</h6>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                    </div>
                </div>
            </div>

            <div class="card-body pt-2 pb-0 px-3 position-relative">
                <label class="text-muted pb-0 mb-0">Full Name</label>
                <div class="text-bold mb-2">
                    <b>{{ $booking->guest ? $booking->guest->details->full_name : '--' }}</b>
                </div>

                @if ($booking->guest && $booking->guest->details->client_number)
                    <label class="text-muted pb-0 mb-0">Client ID</label>
                    <div class="text-bold mb-2">
                        <b>{{ $booking->guest->details->client_number ?? '--' }}</b>
                    </div>
                @endif

                <label class="text-muted pb-0 mb-0">Email</label>
                <div class="text-bold mb-2"><b>{{ $booking->guest ? $booking->guest->details->email : '--' }}</b></div>

                @if ($booking->guest && $booking->guest->details->phone != '' && $booking->guest->details->phone != '---')
                    <label class="text-muted pb-0 mb-0">Phone</label>
                    <div class="text-bold mb-2"><b>{{ $booking->guest->details->phone }}</b></div>
                @endif

                @if($booking->comments && $booking->comments != '')
                    <label class="text-muted pb-0 mb-0">Comments <i class="fad text-info fa-exclamation-circle"></i></label>
                    <div class="mt-1 mb-3 d-flex justify-content-start align-items-center font-weight-bold">
                        {{$booking->comments}}
                    </div>
                @endif
            </div>
        </div>

        @if ($booking->guests->count())
            <div class="card bg-white new">
                <div class="card-header new header-elements-inline">
                    <h6 class="card-title new">Guests</h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 position-relative">
                    @foreach($booking->guests as $og)
                        <div class="py-2 px-3 {{!$loop->first ? 'border-top-1 border-alpha-grey' : ''}} position-relative">
                            <label class="text-muted pb-0 mb-0 d-flex justify-content-between align-items-center">
                                Guest {{$loop->iteration}}
                            </label>
                            <div class="text-bold mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="mb-1"><b>{{$og->full_name}}</b> {{ $og->weight ? '('. $og->weight .' kg)' : '' }}</span>
                                    <span class="text-muted"></span>

                                    @if (!$is_deleted && !$is_cancelled)
                                        <a href="{{ route('tenant.classes.bookings.sessions.edit', ['ref' => $booking->ref, 'id' => $og->id]) }}" title="" class="ml-auto text-slate"><i class="fa fa-edit"></i></a>
                                    @endif
                                </div>
                            </div>

                            <div class="text-muted pb-0 mb-0">Email</div>
                            <div class="mb-2 font-weight-bold">{{ $og->email }}</div>

                            <div class="text-muted pb-0 mb-0">Session</div>
                            <div class="mb-2 font-weight-bold">{{ $og->session->name }}</div>

                            <div class="text-muted pb-0 mb-0">Date</div>
                            <div class="mb-2 font-weight-bold">{{ $og->date->format('D, d M y') }} {{ $og->schedule->start_formatted }} - {{ $og->schedule->end_formatted }}</div>
                        </div>
                    @endforeach
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
                                    <a href="{{ route('tenant.classes.bookings.download_invoice_snapshot', ['ref' => $booking->ref, 'history_id' => $history->id]) }}">
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
    </div>
</div>
