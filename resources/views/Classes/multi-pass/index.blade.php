@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item active">Class Multi Pass</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper">
        <div class="content">
            <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                <h4 class="m-0 mr-auto"><i class="far fa-fw fa-ticket mr-1"></i> Class Multi Pass</h4>
                <a href="{{ route('tenant.classes.multi-pass.create') }}" title="" class="btn bg-danger">
                    <i class="far fa-plus mr-1"></i> New Pass
                </a>
            </div>

            <div class="card">
                <table class="table table-xs table-compact">
                    <thead>
                        <tr class="bg-grey-700">
                            <th>Name</th>
                            <th>Type</th>
                            <th>Code</th>
                            <th>Limit To</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right" width="10%">Price</th>
                            <th class="text-center" width="5%">Active</th>
                            <th class="text-center" width="5%">Usage</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($passes as $pass)
                            <tr>
                                <td valign="left">
                                    <a href="{{ route('tenant.classes.multi-pass.show', ['id' => $pass->id]) }}" class="text-danger font-weight-bold">
                                        {{ $pass->name }}
                                    </a>
                                </td>
                                <td><b>{{ $pass->type }}</b></td>
                                <td>
                                    {!! $pass->code ? '<code>'. $pass->code .'</code>' : '-' !!}
                                    @if ($pass->type === "VOUCHER" && $pass->code)
                                        <span class="d-block mt-1 font-italic">Generated at: {{ $pass->code_generated_at?->format('d.m.Y') ?? $pass->created_at->format('d.m.Y') }}</span>
                                    @endif
                                </td>
                                <td>
                                    {!! $pass->class_session_id ? '<b>'. $pass->session->category->name .'</b> <i class="fal fa-angle-right mx-1"></i> '. $pass->session?->name : '-' !!}
                                </td>
                                <td class="text-right">
                                    @if ($pass->type == 'CREDIT')
                                        &euro; {{ $pass->amount }}
                                    @elseif ($pass->type == 'SESSION')
                                        {{ number_format($pass->amount) }} session
                                    @else
                                    {!! $pass->amount_type == 'VALUE' ? '&euro;' : '' !!} {{ $pass->amount }}{{ $pass->amount_type == 'PERCENTAGE' ? '%' : '' }}
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if ($pass->price <= 0)
                                        FREE
                                    @else
                                        &euro; {{ number_format($pass->price) }}
                                    @endif
                                </td>
                                <td class="text-center">{!! $pass->is_active ? '<i class="far fa-fw fa-check text-success"></i>' : '<i class="fal fa-fw fa-times text-danger"></i>' !!}</td>
                                <td class="text-center">{{ $pass->usage }}</td>
                                <td class="text-right">
                                    <div class="list-icons">
                                        <a href="javascript:void(0)" class="list-icons-item text-success tippy" data-tippy-content="Cash Order" data-toggle="modal" data-target="#modal_add_cash_order_{{ $pass->id }}"><i class="fa-solid fa-money-from-bracket fa-lg"></i></a>
                                        <a href="{{ route('tenant.classes.multi-pass.show', [ 'id' => $pass->id]) }}" class="list-icons-item text-grey tippy" data-tippy-content="Edit"><i class="icon-pencil7"></i></a>
                                        <a href="{{ route('tenant.classes.multi-pass.destroy', [ 'id' => $pass->id]) }}" class="list-icons-item text-danger confirm-dialog tippy" data-tippy-content="Delete" data-text="Delete this pass?"><i class="icon-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-left">No pass found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-md-flex justify-content-end align-items-center">
                @can('export bookings')
                    <a href="{{(request()->fullUrl()) . (request()->has('_token') ? '&' : '?')}}export=true" title="" class="btn btn-success d-block d-md-inline-block mt-2">
                        <i class="fa fa-fw fa-file-excel"></i> Export to Excel
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@foreach ($passes as $pass)
    @include('Classes.partials.multi-pass.add-cash-order')
@endforeach

@endsection
