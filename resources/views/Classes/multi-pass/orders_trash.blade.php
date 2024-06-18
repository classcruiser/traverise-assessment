@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
                <a href="{{ route('tenant.classes.multi-pass.index') }}" class="breadcrumb-item">Multi Passes</a>
                <span class="breadcrumb-item active">Deleted Multi Pass Orders</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper">
            <div class="content">
                <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                    <h4 class="m-0 mr-auto d-none d-md-block"><i class="fal fa-fw fa-ticket-simple mr-1"></i> Deleted Multi Pass Orders</h4>
                </div>

                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-xs table-compact dark">
                            <thead>
                            <tr class="bg-grey-700">
                                <th>Ref</th>
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
                                            <b class="text-dark">{{ $order->ref }}</b>
                                        </div>
                                    </td>
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
            </div>
        </div>
    </div>
@endsection
