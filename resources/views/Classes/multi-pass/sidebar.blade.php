<div class="sidebar sidebar-booking sidebar-main sidebar-expand-md align-self-start ml-3 new">
    <div class="sidebar-content">

        <div class="card bg-white new">
            <div class="card-header new header-elements-inline">
                <h6 class="card-title new">Guest {!! (!$order->guest) ? '<span><i class="far fa-exclamation-triangle"></i></span>' : '<span><i class="far fa-check is-green"></i></span>' !!}</h6>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                    </div>
                </div>
            </div>

            <div class="card-body pt-2 pb-0 px-3 position-relative">
                <label class="text-muted pb-0 mb-0">Full Name</label>
                <div class="text-bold mb-2">
                    <b>{{ $order->guest ? $order->guest->full_name : '--' }}</b>
                </div>

                @if ($order->guest && $order->guest->client_number)
                    <label class="text-muted pb-0 mb-0">Client ID</label>
                    <div class="text-bold mb-2">
                        <b>{{ $order->guest->client_number ?? '--' }}</b>
                    </div>
                @endif

                <label class="text-muted pb-0 mb-0">Email</label>
                <div class="text-bold mb-2"><b>{{ $order->guest ? $order->guest->email : '--' }}</b></div>

                @if ($order->guest && $order->guest->phone != '' && $order->guest->phone != '---')
                    <label class="text-muted pb-0 mb-0">Phone</label>
                    <div class="text-bold mb-2"><b>{{ $order->guest->phone }}</b></div>
                @endif
            </div>
        </div>
    </div>
</div>
