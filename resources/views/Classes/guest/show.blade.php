@extends('Booking.app')

@section('content')

    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item"><a href="{{ route('tenant.classes.guests.index') }}" title="" class="text-grey">Guests</a></span>
                <span class="breadcrumb-item active">{{ $record->full_name }}</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                <div class="card">
                    <div class="card-header header-elements-inline bg-transparent">
                        <h4 class="card-title">
                            {{ $record->full_name }}
                        </h4>
                        <div class="header-elements">
                            <a href="{{ route('tenant.classes.guests.index') }}" title="" class="btn btn-link text-slate">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                            <a href="{{ route('tenant.classes.guests.edit', ['guest' => $record]) }}" class="btn bg-slate">Edit</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <p><b>CUSTOMER DETAILS</b></p>
                                <table width="100%" class="cust-table">
                                    <tr>
                                        <td width="40%">Client ID</td>
                                        <td width="60%" class="text-info">{{ $record->client_number ?? '---' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Full Name</td>
                                        <td>{{ $record->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Company</td>
                                        <td>{{ $record->company != '' ? $record->company : '---' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td><a href="mailto:{{ $record->email }}" class="text-danger">{{ $record->email }}</a></td>
                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td>{{ $record->phone != '' ? $record->phone : '---' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-4">
                                <p><b>ADDRESS</b></p>
                                <table width="100%" class="cust-table">
                                    <tr>
                                        <td width="40%">Street</td>
                                        <td width="60%">{{ $record->street != '' ? $record->street : '---' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Zip Code</td>
                                        <td>{{ $record->zip }}</td>
                                    </tr>
                                    <tr>
                                        <td>City</td>
                                        <td>{{ $record->city != '' ? $record->city : '---' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Country</td>
                                        <td>{{ $record->country != '' ? $record->country : '---' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-4">
                                <p><b>OTHER</b></p>
                                <table width="100%" class="cust-table">
                                    <tr>
                                        <td width="40%">Total Bookings</td>
                                        <td width="60%">{{ $record->classes_count }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Multi Pass Orders</td>
                                        <td>{{ $record->passes_count }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="pt-2 px-3">
                            <h5>Bookings</h5>
                        </div>

                        <table class="table table-xs table-compact dark">
                            <thead>
                                <tr class="bg-grey-700">
                                    <th>Ref</th>
                                    <th width="15%">Status</th>
                                    <th class="text-center">Guests</th>
                                    <th class="text-center">Sessions</th>
                                    <th>Booked</th>
                                    <th>Price</th>
                                    <th class="text-center">With Pass</th>
                                    <th class="text-center">With Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classes as $b)
                                    @if($b->booking?->guest)
                                        <tr>
                                            <td>
                                                <a href="{{ route('tenant.classes.bookings.show', [ 'ref' => $b->booking->ref ]) }}" class="text-danger"><b>{{ $b->booking->ref }}</b></a>
                                            </td>
                                            <td>
                                                <span class="badge {{ $b->booking->status_badge }} badge-pill">{{ $b->booking->booking_status }}</span>
                                            </td>
                                            <td class="text-center">{{ $b->booking->people }}</td>
                                            <td class="text-center">{{ $b->booking->sessions->count() }}</td>
                                            <td>{{ $b->booking->booking_date->format('d.m.Y') }}</td>
                                            <td>
                                                @if(is_float($b->booking->payment->total) || is_int($b->booking->payment->total))
                                                    <b>&euro;{{ $b->booking->payment->total }}</b>
                                                @else
                                                    --
                                                @endif
                                            </td>
                                            <td class="text-center">{!! $b->booking->pass ? '<i class="fa fa-fw fa-check text-success"></i>' : '-' !!}</td>
                                            <td class="text-center">{!! $b->booking->pass?->type == 'CREDIT' ? '<i class="fa fa-fw fa-check text-success"></i>' : '-' !!}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>

                        @if ($classes->hasPages())
                            <hr class="my-2" />
                            <div class="d-md-flex justify-content-center align-items-center px-3 pb-2">
                                <div>{{ $classes->appends($_GET)->links() }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="pt-2 px-3">
                            <h5>Mutli Pass Orders</h5>
                        </div>

                        <table class="table table-xs table-compact dark">
                            <thead>
                                <tr class="bg-grey-700">
                                    <th>Ref</th>
                                    <th width="15%">Status</th>
                                    <th>Multi Pass</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($passes as $pass)
                                    <tr>
                                        <td>
                                            <a href="{{ route('tenant.classes.multi-pass.order', [ 'order' => $pass->id ]) }}" class="text-danger"><b>{{ $pass->ref }}</b></a>
                                        </td>
                                        <td>
                                            <span class="order-status status-{{ strtolower($pass->status) }}">
                                                {{ $pass->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('tenant.classes.multi-pass.show', [ 'id' => $pass->multiPass->id ]) }}" title="" class="text-danger">
                                                <b>{{ $pass->multiPass->name }}</b>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            @if(is_float($pass->total) || is_int($pass->total))
                                                <b>&euro;{{ $pass->total }}</b>
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td class="text-center"><span class="method-{{ $pass->methods ?? '-' }}">{{ $pass->methods ?? '-' }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if ($passes->hasPages())
                            <hr class="my-2" />
                            <div class="d-md-flex justify-content-center align-items-center px-3 pb-2">
                                <div>{{ $passes->appends($_GET)->links() }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
