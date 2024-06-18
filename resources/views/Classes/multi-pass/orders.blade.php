@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
                <a href="{{ route('tenant.classes.multi-pass.index') }}" class="breadcrumb-item">Multi Passes</a>
                <a href="{{ route('tenant.classes.multi-pass.orders') }}" class="breadcrumb-item active">Multi Pass Orders</a>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper">
            <div class="content">
                <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                    <h4 class="m-0 mr-auto d-none d-md-block"><i class="fal fa-fw fa-ticket-simple mr-1"></i> All Multi Pass Orders</h4>
                    <button class="btn btn-labeled btn-labeled-left bg-orange-400 ml-1 collapsed" data-toggle="collapse" href="#advanced-search">
                        <b><i class="icon-search4"></i></b> Advanced Search
                    </button>
                </div>
                @include('Classes.partials.multi-pass.advanced-search')

                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-xs table-compact dark">
                            <thead>
                            <tr class="bg-grey-700">
                                <th>Ref</th>
                                <th>Order Date</th>
                                <th>Multi Pass</th>
                                <th>Guest</th>
                                <th>Email</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Payment Method</th>
                                <th class="text-center">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center">
                                            <a href="{{ route('tenant.classes.multi-pass.order', [ 'order' => $order->id ]) }}" class="text-dark">
                                                <b>{{ $order->ref }}</b>
                                            </a>
                                        </div>
                                    </td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('tenant.classes.multi-pass.show', [ 'id' => $order->multiPass->id ]) }}" title="" class="text-danger">
                                            <b>{{ $order->multiPass->name }}</b>
                                        </a>
                                    </td>
                                    <td><b>{{ $order->guest->full_name ?? '-' }}</b></td>
                                    <td>{{ $order->guest->email ?? '-' }}</td>
                                    <td class="text-center">
                                        @if(is_float($order->total) || is_int($order->total))
                                            <b>&euro;{{ $order->total }}</b>
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td class="text-center"><span class="method-{{ $order->methods ?? '-' }}">{{ $order->methods ?? '-' }}</span></td>
                                    <td class="text-center">
                                        <span class="order-status status-{{ strtolower($order->status) }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-md-flex justify-content-between align-items-center">
                    <div>{{$orders->appends($_GET)->links()}}</div>
                    @can('export bookings')
                        <a href="{{(request()->fullUrl()) . (request()->has('_token') ? '&' : '?')}}export=true" title="" class="btn btn-success d-block d-md-inline-block mt-2">
                            <i class="fa fa-fw fa-file-excel"></i> Export to Excel
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $('.date-basic').daterangepicker({
            autoApply: false,
            autoUpdateInput: false,
            singleDatePicker: true,
            locale: {
                format: 'DD.MM.YYYY',
                cancelLabel: "Clear"
            }
        });
        $('.date-basic').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD.MM.YYYY'));
        });
    </script>
@endsection
