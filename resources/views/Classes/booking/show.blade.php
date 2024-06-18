@extends('Booking.app')

@section('content')
    <div class="App-class-bookings">

        @include('Classes.partials.bookings.approve-booking')

        @if($booking->is_confirmed && blank($booking->class_multi_passes_id) && $multiPasses)
            @include('Classes.partials.bookings.add-multi-pass')
        @endif

        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
                    <a href="{{ route('tenant.classes.bookings.index') }}" class="breadcrumb-item">Classes</a>
                    <span class="breadcrumb-item active"># {{ $booking->ref }}</span>
                </div>

                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>

        <div class="page-content new pt-4">

            @include('Classes.booking.sidebar')

            <div class="content-wrapper container reset">
                <div class="content pt-0">

                    <div class="d-flex justify-content-between mb-3">
                        <div class="left-toolbar">
                            @if (!$is_deleted && !$is_cancelled)
                                <a href="{{ route('tenant.classes.bookings.sessions.index', ['ref' => $booking->ref]) }}" class="btn btn-sm bg-danger-400"><i class="fa fa-fw fa-user"></i> Add session</a>
                                <a href="{{ route('tenant.classes.bookings.addons.index', ['ref' => $booking->ref]) }}" class="btn btn-sm bg-danger-400"><i class="fa fa-fw fa-layer-plus"></i> Manage Add on</a>
                                @if($booking->is_confirmed && blank($booking->class_multi_passes_id) && $multiPasses)
                                    <button class="btn btn-sm bg-danger-400" data-toggle="modal" data-target="#modal_add_multi_pass">
                                        <i class="fa fa-fw fa-ticket"></i> Add Multi Pass
                                    </button>
                                @endif
                            @endif
                        </div>
                        <div class="right-toolbar d-flex justify-content-end align-items-center">
                            @if($booking->status != 'CANCELLED' && $booking->status != 'EXPIRED' && !$is_deleted)
                                <button class="btn bg-danger-400 btn-sm rounded" data-toggle="dropdown">Manage <i class="fal fa-angle-down ml-1"></i></button>
                            @endif
                            <div class="dropdown-menu dropdown-menu-sm">
                                @if($booking->status != 'CONFIRMED')
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#modal_approve_booking" data-ref="{{$booking->ref}}"><i class="fal fa-fw fa-check mr-1"></i> Approve</a>
                                @else
                                    <a href="{{ route('tenant.payment.class.show', ['id' => $booking->payment->link]) }}" class="dropdown-item" target="_blank">
                                        <i class="fal fa-fw fa-money-bill-alt mr-1"></i> Go to payment page
                                    </a>

                                    <a href="#" class="dropdown-item class-cancel-booking" data-ref="{{ $booking->ref }}">
                                        <i class="fal fa-fw fa-times mr-1"></i> Cancel booking
                                    </a>
                                @endif
                                @can('delete booking')
                                    <a href="{{ route('tenant.classes.bookings.delete', ['ref' => $booking->ref]) }}" class="dropdown-item confirm-dialog" data-text="DELETE BOOKING?"><i class="fa fa-fw fa-trash mr-1"></i> Delete Booking</a>
                                @endcan
                            </div>

                            @can('download PDF')
                                <a href="{{ route('tenant.classes.bookings.pdf-invoice', ['ref' => $booking->ref]) }}" class="btn btn-sm bg-danger-800 ml-1"><i class="fa fa-fw fa-file-pdf"></i> Download PDF</a>
                            @endcan
                        </div>
                    </div>

                    <div class="card booking-details">
                        <div class="card-header alpha-grey header-elements-inline">
                            <h6 class="card-title"><i class="fa fa-clipboard mr-1"></i> <b>Booking Overview</b></h6>
                            <div class="header-elements">
                                <div class="list-icons">
                                    <a class="list-icons-item rotate-180" data-action="collapse"></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    @if ($booking->guest)
                                        <p>
                                            <a href="{{ route('tenant.classes.guests.show', [ 'id' => $booking->guest->details->id ]) }}" title="" class="text-danger"><b>{{ $booking->guest->details->full_name }}</b></a>
                                            <br />
                                            @if ($booking->guest->details->client_number)
                                                Client ID: <b class="text-info">{{ $booking->guest->details->client_number }}</b>
                                                <br />
                                            @endif
                                            @if($booking->guest->details->street != '')
                                                {{$booking->guest->details->street}}, {{$booking->guest->details->city}} {{$booking->guest->details->zip}}
                                                <br />
                                            @endif
                                            @if($booking->guest->details->phone != '' && $booking->guest->details->phone != '---')
                                                Phone: {{$booking->guest->details->phone}}
                                                <br />
                                            @endif
                                            {!! $booking->guest->details->country != '' ? $booking->guest->details->country .'<br />' : '' !!}
                                            <a href="#" title="" class="text-danger">{{$booking->guest->details->email}}</a>
                                        </p>
                                    @endif
                                </div>
                                <div class="text-muted text-uppercase font-size-sm d-flex flex-column justify-content-end align-items-start" style="line-height: 1.75">
                                    <span class="ml-auto">Created on <b class="text-slate">{{ $booking->created_at->format('d.m.Y H:i:s') }}</b></span>
                                    <span class="ml-auto">Booking ID: <b class="text-slate">{{ $booking->id }}</b></span>
                                    <span class="ml-auto">Booking Ref: <b class="text-slate">#{{ $booking->ref }}</b></span>
                                    <span class="ml-auto">STATUS: <b class="text-slate">{{ $is_deleted ? 'DELETED' : $booking->status }}</b></span>
                                    <span class="ml-auto badge {{ !$is_deleted ? $booking->status_badge : 'bg-danger' }} text-uppercase font-size-sm">
                                        {{ !$is_deleted ? $booking->booking_status : 'DELETED' }}
                                    </span>
                                    <div class="ml-auto mt-1 text-success">
                                        @if ($booking->has_check_in && $booking->checked_in_at)
                                            <i class="fa fa-check-circle"></i> CHECKED IN: <b>{{ $booking->checked_in_at->format('d.m.Y, H:i:s') }}</b>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('tenant.classes.bookings.update_price', ['ref' => $booking->ref]) }}" method="POST" id="session-details">
                            <table class="table table-xs new">
                                <thead>
                                    <tr class="bg-grey-700">
                                        <th>Guest</th>
                                        <th>Class</th>
                                        <th>Date</th>
                                        <th>Instructor</th>
                                        <th class="text-right">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($booking->sessions as $session)
                                        <tr>
                                            <td>
                                                @if (! blank($session->check_in_at))
                                                    <i class="fas fa-fw fa-circle-check mr-1 text-success tippy" data-tippy-content="{{ $session->check_in_at->format('d.m.Y H:i') }}"></i>
                                                @endif
                                                <b>{{ $session->full_name }}</b>
                                            </td>
                                            <td>
                                                <b>{{ $session->session->category->short_name }} {{ $session->session->name }}</b>
                                            </td>
                                            <td>{{ $session->date->format('l, d M y') }}, {{ $session->schedule->start_formatted }} - {{ $session->schedule->end_formatted }}</td>
                                            <td>{{ $session->instructor?->name ?? '-' }}</td>
                                            <td class="text-right">
                                                <div class="d-flex justify-content-end align-items-center">
                                                    <div class="input-group input-group-sm" style="width: 110px">
                                                        <span class="input-group-prepend">
                                                            <span class="input-group-text">&euro;</span>
                                                        </span>
                                                        <input
                                                            type="text"
                                                            name="prices[{{ $session->id }}]"
                                                            class="form-control form-control-sm"
                                                            id="current-room-price"
                                                            value="{{ $session->price }}"
                                                            {{!auth()->user()->can('edit prices') || $is_deleted || $is_cancelled ? 'readonly' : ''}}
                                                        />
                                                        <input type="hidden" name="old_prices[{{ $session->id }}]" value="{{ $session->price }}" />
                                                    </div>
                                                    @if (!$is_deleted && !$is_cancelled)
                                                        <a
                                                            href="{{ route('tenant.classes.bookings.delete-session', ['ref' => $booking->ref, 'id' => $session->id]) }}"
                                                            title=""
                                                            class="d-block ml-2 text-danger tippy confirm-dialog"
                                                            data-tippy-content="Delete session"
                                                            data-text="Delete this session?"
                                                        >
                                                            <i class="icon-cross2"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No class added</td>
                                        </tr>
                                    @endforelse
                                    @forelse($booking->addons as $addon)
                                        <tr>
                                            <td colspan="2">
                                                <i class="fa fa-gift fa-fw mr-1 text-danger-300 tippy" data-tippy-content="Extra / Addon"></i> {{$addon->addon->name}}
                                            </td>
                                            <td class="text-left">
                                                @if($addon->addon->rate_type == 'Day')
                                                    {{intVal($addon->amount)}} {{$addon->addon->unit_name}}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{$addon->amount}} <i class="far fa-user"></i>
                                            </td>
                                            <td class="text-right">
                                                @can('see prices')
                                                    <div class="d-flex justify-content-end align-items-center">
                                                        <div class="input-group input-group-sm" style="width: 110px">
                                                            <span class="input-group-prepend">
                                                                <span class="input-group-text">&euro;</span>
                                                            </span>
                                                            <input
                                                                type="text" name="addons[{{$addon->id}}][price]"
                                                                class="form-control form-control-sm"
                                                                value="{{ $addon->price }}"
                                                                {{!auth()->user()->can('edit prices') || $is_deleted || $is_cancelled ? 'readonly' : ''}}
                                                            />
                                                        </div>
                                                        @if (!$is_deleted && !$is_cancelled)
                                                            <a
                                                                href="javascript:void(0)"
                                                                title=""
                                                                class="ml-2 text-danger tippy remove-session-addon"
                                                                data-id="{{ $addon->id }}"
                                                                data-url="{{ route('tenant.classes.bookings.addons.destroy', ['id' => $addon->id, 'ref' => $booking->ref]) }}"
                                                                data-tippy-content="Delete addon"
                                                            >
                                                                <i class="icon-cross2"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @else
                                                    --
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No addons added</td>
                                        </tr>
                                    @endforelse
                                    <tr>
                                        <td class="text-right" colspan="4"><b>SUBTOTAL</b></td>
                                        <td class="text-right">
                                            @can('see prices')
                                                <b>&euro;{{number_format($booking->total_price, 2)}}</b>
                                            @else
                                                --
                                            @endcan
                                        </td>
                                    </tr>
                                    @if ($booking->discount_value && $booking->class_multi_passes_id && !$booking->class_multi_pass_payment_id)
                                        <tr>
                                            <td class="text-right" colspan="4"><b>VOUCHER</b> ({{ $booking->pass->code }})</td>
                                            <td class="text-right">
                                                @can('see prices')
                                                    <b>- &euro;{{number_format($booking->discount_value, 2)}}</b>
                                                @else
                                                    --
                                                @endcan
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($booking->discount_value && $booking->class_multi_passes_id && $booking->class_multi_pass_payment_id)
                                        <tr>
                                            <td class="text-right" colspan="4"><b>MULTI PASS</b> ({{ $booking->pass->name }})</td>
                                            <td class="text-right">
                                                @can('see prices')
                                                    <b>- &euro;{{number_format($booking->discount_value, 2)}}</b>
                                                @else
                                                    --
                                                @endcan
                                            </td>
                                        </tr>
                                    @endif
                                    @if($taxes['cultural_tax_percent'] && $taxes['cultural_tax_percent'] > 0)
                                        <tr>
                                            <td class="text-right" colspan="4"><b>{{$taxes['cultural_tax_percent']}}% CULTURAL TAX</b></td>
                                            <td class="text-right">
                                                @can('see prices')
                                                    <b>&euro;{{ $booking->room_tax }}</b>
                                                @else
                                                    --
                                                @endcan
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="text-right" colspan="4"><b>GRAND TOTAL</b></td>
                                        <td class="text-right">
                                            @can('see prices')
                                                <b>&euro;{{number_format($booking->grand_total, 2)}}</b>
                                            @else
                                                --
                                            @endcan
                                        </td>
                                    </tr>
                                    @if(($taxes['hotel_tax_percent'] && $taxes['hotel_tax_percent'] > 0) && ($taxes['goods_tax_percent'] && $taxes['goods_tax_percent'] > 0))
                                        <tr>
                                            <td class="text-right" colspan="4" valign="top" style="vertical-align: top;"><b>VAT</b></td>
                                            <td class="text-right">
                                                @can('see prices')
                                                    {{$taxes['hotel_tax_percent']}}% <b>&euro;{{number_format($taxes['hotel_tax'], 2)}}</b>
                                                @else
                                                    --
                                                @endcan
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-right" colspan="4" valign="top" style="vertical-align: top;">&nbsp;</td>
                                            <td class="text-right">
                                                @can('see prices')
                                                    {{$taxes['goods_tax_percent']}}% <b>&euro;{{number_format($taxes['goods_tax'], 2)}}</b>
                                                @else
                                                    --
                                                @endcan
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="text-right" colspan="4"><b>TOTAL PAID</b></td>
                                        <td class="text-right">
                                            @can('see prices')
                                                <b>&euro;{{number_format($booking->payment->total_paid, 2)}}</b>
                                            @else
                                                --
                                            @endcan
                                        </td>
                                    </tr>
                                    @if(auth()->user()->hasRole('Agent') || $booking->agent_id)
                                        <tr>
                                            <td class="text-right" colspan="4"><b>AGENT COMMISSION</b></td>
                                            <td class="text-right">
                                                @can('see prices')
                                                    {{$booking->agent->commission_value}}% <b>&euro;{{number_format($booking->commission, 2)}}</b>
                                                @else
                                                    --
                                                @endcan
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="text-right text-danger" colspan="4"><b>OPEN BALANCE</b></td>
                                        <td class="text-right text-danger"><b>&euro;{{number_format($booking->payment->open_balance, 2)}}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                            @if($booking->guests_count > 0 && !$is_deleted && !$is_cancelled)
                                @can('edit prices')
                                    <div class="d-flex justify-content-end p-3">
                                        @csrf
                                        <button class="btn bg-danger-400 btn-session-price" data-reload="1"><i class="far fa-fw fa-check"></i> Update Price</button>
                                    </div>
                                @endcan
                            @endif
                        </form>
                    </div>

                    @if($booking->status == 'CONFIRMED' && auth()->user()->can('add payment'))
                        <div class="card">
                            <div class="card-header alpha-grey header-elements-inline">
                                <h6 class="card-title"><i class="fa fa-dollar-sign mr-1"></i> <b>Payment</b></h6>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item rotate-180" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body border-0 p-0">
                                <div class="text-uppercase font-size-lg p-3"><b>Payment History</b></div>
                                <form method="POST" action="{{ route('tenant.classes.bookings.payments.store', ['id' => $booking->payment->id]) }}" id="new-payment">
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
                                            @if($booking->payment->records->count() <= 0)
                                                <tr>
                                                    <td colspan="7">No payment records yet.</td>
                                                </tr>
                                            @else
                                                @foreach($booking->payment->records as $record)
                                                    <tr>
                                                        <td>{{ $record->created_at->format('d.m.Y H:i') }}</td>
                                                        <td><span class="text-uppercase"><b>{{ $record->methods }}</b></span></td>
                                                        <td class="text-center">
                                                            @can('see prices')
                                                                <div class="input-group input-group-sm" style="width: 126px">
                                                                    <span class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="fa fa-euro-sign"></i></span>
                                                                    </span>
                                                                    <input
                                                                        type="text"
                                                                        name="price"
                                                                        class="form-control form-control-sm payment-record-{{ $record->id }}"
                                                                        placeholder="0.0"
                                                                        required
                                                                        value="{{ $record->amount }}"
                                                                        {{ !$is_deleted && !$is_cancelled ? '' : 'readonly' }}
                                                                    />
                                                                    @if (!$is_deleted && !$is_cancelled)
                                                                        <span class="input-group-append">
                                                                            <button class="btn btn-sm btn-danger admin-update-payment-record" data-id="{{ $record->id }}" data-url="{{ route('tenant.classes.bookings.payments.update', ['id' => $record->id]) }}"><i class="far fa-check"></i></button>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <b>--</b>
                                                            @endcan
                                                        </td>
                                                        <td>{{ !is_null($record->paid_at) ? $record->paid_at->format('d.m.Y') : '-' }}</td>
                                                        <td>
                                                            @if (in_array($record->methods, ['stripe', 'paypal', 'voucher', 'pass']) && $record->status == 'paid')
                                                                SYSTEM
                                                            @else
                                                                <b>{{ $record->verify_by ? $record->user->name : '--' }}</b>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $record->verified_at ? $record->verified_at->format('d.m.Y H:i') : '--' }}
                                                            @if ($record->methods == 'stripe' && $record->status == 'paid')
                                                                <br />
                                                                <code class="p-0">{{ $record->data['id'] ?? '-' }}</code>
                                                            @endif
                                                        </td>
                                                        <td class="text-right">
                                                            @if (!$is_deleted && !$is_cancelled)
                                                                <div class="d-flex justify-content-end align-items-center">
                                                                    @if (!$record->verify_by && !$record->verified_at)
                                                                        <button title="" class="btn btn-sm bg-grey-600 py-1" v-on:click.prevent="loadPaymentRecord({{ $record->id }}, {{ $loop->iteration }})">VIEW</button>
                                                                    @else
                                                                        <a href="#" class="btn btn-sm alpha-grey btn-disabled text-muted tippy" data-tippy-content="Verified"><i class="fal fa-check text-success"></i></span></a>
                                                                        @can('send payment confirmation')
                                                                            <button href="#" class="btn btn-sm btn-danger tippy confirm-dialog send-confirmed-payment-email" data-tippy-content="resend confirmed payment email" data-text="Send confirmed payment email to this guest?" data-url="{{ route('tenant.classes.bookings.payments.send_confirm_email', ['id' => $record->id]) }}" data-transfer-id="{{ $record->id }}">
                                                                                <i class="fa fa-fw fa-envelope"></i>
                                                                            </button>
                                                                        @endcan
                                                                    @endif
                                                                    @can('delete payment')
                                                                        <a href="{{ route('tenant.classes.bookings.payments.destroy', ['id' => $record->id]) }}" class="text-danger ml-2 tippy confirm-dialog" data-tippy-content="Delete record" data-text="DELETE RECORD? This action will be logged"><i class="fa fa-times fa-fw"></i></a>
                                                                    @endcan
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            @can('add payment')
                                                @if (!$is_deleted && !$is_cancelled)
                                                    <tr class="alpha-grey">
                                                        <td colspan="7" class="px-3 py-2"><i class="fal fa-plus fa-fw"></i> <b>NEW PAYMENT</b></td>
                                                    </tr>
                                                    <tr class="alpha-grey">
                                                        <th colspan="3"><b>METHOD</b></th>
                                                        <th><b>AMOUNT</b></th>
                                                        <th colspan="2"><b>PAID AT</b></th>
                                                        <th></th>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="py-3">
                                                            <select class="form-control select-no-search form-control-sm" data-container-css-class="select-sm" data-fouc data-placeholder="Method" name="methods">
                                                                <option value="cash">CASH</option>
                                                                <option value="creditcard">CREDIT CARD</option>
                                                                <option value="banktransfer">BANK TRANSFER</option>
                                                                <option value="ec-zahlung">EC-ZAHLUNG</option>
                                                            </select>
                                                        </td>
                                                        <td class="py-3"><input type="text" name="amount" class="form-control form-control-sm amount-payment" placeholder="amount" style="width: 100px;" required /></td>
                                                        <td colspan="2" class="py-3">
                                                            <div class="input-group input-group-sm" style="width: 150px">
                                                                <span class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                                                </span>
                                                                <input type="text" class="form-control date-basic" data-ref="{{ $booking->ref }}" value="{{ date('d.m.Y') }}" name="paid_at">
                                                            </div>
                                                        </td>
                                                        <td class="text-right py-3">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm bg-grey-600 py-1 btn-add-payment">ADD</button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endcan
                                        </tbody>
                                    </table>
                                </form>
                            </div>

                        </div>
                    @endif

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header alpha-grey header-elements-inline">
                                    <h6 class="card-title"><i class="far fa-history mr-1"></i> <b>Booking History</b></h6>
                                    <div class="header-elements">
                                        <div class="list-icons">
                                            <a class="list-icons-item rotate-180" data-action="collapse"></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                    <div class="list-feed">
                                        @foreach($booking->histories as $history)
                                            @if($booking->archived && $history->containsAmount)
                                            @else
                                                <div class="list-feed-item border-{{$history->info_type}}">
                                                    <div class="text-muted">{{$history->created_at->format('M d, H:i:s')}}</div>
                                                    {!! $history->details !!}
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header alpha-grey header-elements-inline">
                                    <h6 class="card-title"><i class="far fa-comment-dots mr-1"></i> <b>Internal Notes</b></h6>
                                    <div class="header-elements">
                                        <div class="list-icons">
                                            <a class="list-icons-item rotate-180" data-action="collapse"></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body internal-notes">
                                    <div class="list-feed">
                                        @forelse ($notes as $note)
                                            <div class="list-feed-item">
                                                <div class="text-muted">{{ $note['date'] }}</div>
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <i class="far fa-fw fa-comment-dots"></i> <b>{{ $note['user'] }}</b> : <i>{{ $note['message'] }}</i>
                                                    </div>
                                                    @if (auth()->user()->hasRole('Super Admin') || auth()->user()->id == $note['user_id'])
                                                        <a href="{{ route('tenant.classes.bookings.deleteInternalNote', ['id' => $note['id']]) }}" class="text-danger ml-2 tippy confirm-dialog" data-tippy-content="Delete note" data-text="DELETE NOTE?"><i class="fa fa-times fa-fw"></i></a>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <i>Empty notes</i>
                                        @endif
                                    </div>
                                </div>
                                @if(!$is_deleted)
                                    <div class="card-body">
                                        <form action="{{ route('tenant.classes.bookings.postInternalNotes') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                            <label class="mb-2"><b>Add new note</b></label>
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <textarea class="form-control form-control-sm" rows="3" placeholder="write some note" name="message"></textarea>
                                                </div>
                                            </div>
                                            @can('add notes')
                                                <div class="form-group row mb-0">
                                                    <div class="col-12">
                                                        <div class="justify-content-end d-flex">
                                                            <button class="bg-grey-600 btn">Submit</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endcan
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
    window.bookingRef = '{{ $booking->ref }}';

    $('.date-basic').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        autoApply: true,
        drops: 'up',
        locale: {
            cancelLabel: 'Clear',
            format: 'DD.MM.YYYY'
        }
    });

    $('.date-basic').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD.MM.YYYY'));
    });


    $('.date-time-picker').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        timePicker: true,
        timePicker24Hour: true,
        autoApply: true,
        locale: {
            cancelLabel: 'Clear',
            format: 'DD.MM.YYYY HH:mm'
        }
    });

    $('.date-time-picker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD.MM.YYYY HH:mm'));
    });

    @if (session()->has('success'))
        new PNotify({
            title: "Success",
            text: "{{ session()->get('success') }}",
            addclass: "alert alert-styled-left alert-arrow-left bg-success text-white border-success",
            delay: 4000
        });
    @endif
    @if (session()->has('error'))
        new PNotify({
            title: "Error",
            text: "{{ session()->get('error') }}",
            addclass: "alert alert-styled-left alert-arrow-left bg-danger text-white border-danger",
            delay: 4000
        });
    @endif
    </script>
@endsection
