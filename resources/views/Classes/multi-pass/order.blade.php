@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i
                        class="icon-home2 mr-1"></i>
                    Home</a>
                <a href="{{ route('tenant.classes.multi-pass.orders') }}" class="breadcrumb-item">Multi Pass
                    Orders</a>
                <span class="breadcrumb-item active"># {{ $order->ref }}</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content new pt-4">
        @include('Classes.multi-pass.sidebar')

        <div class="content-wrapper container reset">
            <div class="content pt-0">
                <div class="mb-3 d-flex justify-content-between">
                    <div class="right-toolbar d-flex justify-content-end align-items-center">
                        <resend-confirmation :order-id="{{$order->id}}"/>

                    </div>
                    @can('delete booking')
                        <a href="{{ route('tenant.classes.multi-pass.order.delete', $order->id) }}"
                           class="confirm-dialog d-flex dropdown-item justify-content-end"
                           data-text="DELETE MULTI PASS ORDER ?">
                            <i class="fa fa-fw fa-trash mr-1"></i> Delete Multi Pass Order</a>
                    @endcan
                    @can('download PDF')
                        <a href="{{route('tenant.classes.multi-pass.order.downloadPDFInvoice', $order->id)}}"
                           class="btn btn-sm bg-danger-800 ml-1 w-25">
                            <i class="fa fa-fw fa-file-pdf"></i> Download PDF
                        </a>
                    @endcan
                </div>

                <div class="card booking-details">
                    <div class="card-header alpha-grey header-elements-inline">
                        <h6 class="card-title"><i class="fa fa-clipboard mr-1"></i> <b>Order Overview</b>
                        </h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item rotate-180" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                @if ($order->guest)
                                    <p>
                                        <a href="{{ route('tenant.classes.guests.show', [ 'id' => $order->guest->id ]) }}"
                                           title=""
                                           class="text-danger"><b>{{ $order->guest->full_name }}</b></a>
                                        <br/>
                                        @if ($order->guest->client_number)
                                            Client ID: <b
                                                class="text-info">{{ $order->guest->client_number }}</b>
                                            <br/>
                                        @endif
                                        @if($order->guest->street != '')
                                            {{$order->guest->street}}
                                            , {{$order->guest->city}} {{$order->guest->zip}}
                                            <br/>
                                        @endif
                                        @if($order->guest->phone != '' && $order->guest->phone != '---')
                                            Phone: {{$order->guest->phone}}
                                            <br/>
                                        @endif
                                        {!! $order->guest->country != '' ? $order->guest->country .'<br />' : '' !!}
                                        <a href="#" title=""
                                           class="text-danger">{{$order->guest->email}}</a>
                                    </p>
                                @endif

                                @if ($order->is_other_guest)
                                        <p>Order made by<br />
                                            <a href="{{ route('tenant.classes.guests.show', [ 'id' => $order->guestWhoOrdered->id ]) }}"
                                                  title=""
                                                  class="text-danger"><b>{{ $order->guestWhoOrdered->full_name }}</b></a>
                                        </p>
                                @endif
                            </div>
                            <div
                                class="text-muted text-uppercase font-size-sm d-flex flex-column justify-content-end align-items-start">
                                <span class="ml-auto">
                                    Created on <b
                                        class="text-slate">{{ $order->created_at->format('d.m.Y H:i:s') }}</b>
                                </span>
                                <span class="ml-auto">Order ID: <b
                                        class="text-slate">{{ $order->id }}</b></span>
                                <span class="ml-auto">Order Ref: <b
                                        class="text-slate">#{{ $order->ref }}</b></span>
                                <span class="ml-auto">STATUS: <b
                                        class="text-slate">{{ $is_deleted ? 'DELETED' : $order->status }}</b></span>
                                <span
                                    class="ml-auto badge {{ !$is_deleted ? $order->status_badge : 'bg-danger' }} text-uppercase font-size-sm">
                                    {{ !$is_deleted ? $order->status : 'DELETED' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <table class="table table-xs new">
                            <thead>
                            <tr class="bg-grey-700">
                                <th class="two wide">Ref</th>
                                <th>Multi Pass</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Payment</th>
                                <th class="text-center">Remaining</th>
                                <th class="text-right">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center">
                                        <a href="{{ route('tenant.classes.multi-pass.order', [ 'order' => $order->id ]) }}"
                                           class="text-dark">
                                            <b>{{ $order->ref }}</b>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('tenant.classes.multi-pass.show', [ 'id' => $order->multiPass->id ]) }}"
                                       title="" class="text-danger">
                                        <b>{{ $order->multiPass->name }}</b>
                                    </a>
                                </td>
                                <td class="text-center">
                                    @if(is_float($order->total) || is_int($order->total))
                                        <b>&euro;{{ $order->total }}</b>
                                    @else
                                        --
                                    @endif
                                </td>
                                <td class="text-center text-uppercase">{{ $order->methods }}</td>
                                <td class="text-center">
                                    <div class="input-group input-group-sm" style="width: 100px">
                                        <input type="text" name="remaining" class="form-control form-control-sm mp-remaining-{{$order->id}}" placeholder="0.0" value="{{ $order->remaining }}"/>
                                        <span class="input-group-append">
                                            <button class="btn btn-sm btn-danger admin-mp-update-remaining-usage" data-id="{{ $order->id }}">
                                                <i class="far fa-check"></i>
                                            </button>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-right">{{ $order->status }}</td>
                            </tr>
                            <tr>
                                <td class="text-right" colspan="5"><b>SUBTOTAL</b></td>
                                <td class="text-right">
                                    @can('see prices')
                                        <b>&euro;{{number_format($order->total, 2)}}</b>
                                    @else
                                        --
                                    @endcan
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right" colspan="5"><b>TOTAL PAID</b></td>
                                <td class="text-right">
                                    @can('see prices')
                                        <b>&euro;{{number_format($order->total_paid, 2)}}</b>
                                    @else
                                        --
                                    @endcan
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right text-danger" colspan="5"><b>OPEN BALANCE</b></td>
                                <td class="text-right text-danger">
                                    <b>&euro;{{number_format($order->open_balance, 2)}}</b></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header alpha-grey header-elements-inline">
                        <h6 class="card-title"><i class="fa fa-dollar-sign mr-1"></i> <b>Payment</b>
                        </h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item rotate-180" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body border-0 p-0">
                        <div class="text-uppercase font-size-lg p-3"><b>Payment History</b></div>
                        <div>
                            <table class="table table-xs">
                                <thead>
                                <tr class="alpha-grey border-top-1 border-alpha-grey border-bottom-1">
                                    <th class="text-uppercase py-2 px-3">Date</th>
                                    <th class="text-uppercase py-2 px-3">Method</th>
                                    <th class="text-uppercase py-2 px-3 text-right">Amount</th>
                                    <th class="text-uppercase py-2 px-3">Paid at</th>
                                    <th class="text-uppercase py-2 px-3">Verified by</th>
                                    <th class="text-uppercase py-2 px-3">Verified at</th>
                                    <th class="text-right"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($order->records->count() <= 0)
                                    <tr>
                                        <td colspan="7">No payment records yet.</td>
                                    </tr>
                                @else
                                    @foreach($order->records as $record)
                                        <tr>
                                            <td>{{ $record->created_at->format('d.m.Y H:i') }}</td>
                                            <td><span
                                                    class="text-uppercase"><b>{{ $record->methods }}</b></span>
                                            </td>
                                            <td class="text-center">
                                                @can('see prices')
                                                    <div class="input-group input-group-sm justify-content-end">
                                                                <span class="input-group-text">
                                                                        <i class="fa fa-euro-sign"></i> {{ $record->amount }}
                                                                </span>
                                                    </div>
                                                @else
                                                    <b>--</b>
                                                @endcan
                                            </td>
                                            <td>{{ !is_null($record->paid_at) ? $record->paid_at->format('d.m.Y') : '-' }}</td>
                                            <td>
                                                @if (in_array($record->methods, ['stripe', 'paypal']) && $record->status == 'paid')
                                                    SYSTEM
                                                @else
                                                    <b>{{ $record->verify_by ? $record->user->name : '--' }}</b>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $record->verified_at ? $record->verified_at->format('d.m.Y H:i') : '--' }}
                                                @if ($record->methods == 'stripe' && $record->status == 'paid')
                                                    <br/>
                                                    <code class="p-0">{{ $record->session_id }}</code>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                @if ($record->verify_by && $record->verified_at)
                                                    <a href="#"
                                                       class="btn btn-sm alpha-grey btn-disabled text-muted tippy"
                                                       data-tippy-content="Verified"><i
                                                            class="fal fa-check text-success"></i></span></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

