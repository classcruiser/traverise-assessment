@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item active">Vouchers</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            @if (session()->has('messages'))
            <div class="alert bg-green-400 text-white alert-dismissible">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <i class="fa fa-check-circle mr-1"></i> {{ session('messages') }}
            </div>
            @endif
            <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-credit-card mr-1"></i> Vouchers</h4>
                @can ('add setting')
                <a href="{{ route('tenant.vouchers.create') }}" title="" class="btn bg-danger">
                    <i class="far fa-plus mr-1"></i> New Voucher
                </a>
                @endcan
            </div>

            <div class="card">
                <table class="table table-xs table-compact">
                    <thead>
                        <tr class="bg-grey-700">
                            <th>Name</th>
                            <th>Code</th>
                            <th>Limit</th>
                            <th class="text-right">Amount</th>
                            <th class="text-center">Active</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($vouchers->count() <= 0) <tr>
                        <td colspan="8">No vouchers yet</td>
                        </tr>
                        @endif
                        @foreach ($vouchers as $voucher)
                        <tr>
                            <td class="vertical-top">
                                <b><a href="{{ route('tenant.vouchers.show', [ 'id' => $voucher->id ]) }}"
                                        class="list-icons-item text-danger">{{ $voucher->name }}</a></b>
                            </td>
                            <td><code>{{ $voucher->voucher_code }}</code></td>
                            <td><b>{{ $voucher->usage_limit }}</b></td>
                            <td class="text-right">
                                {!! $voucher->amount_type == 'VALUE' ? '&euro;' : '' !!} {{ $voucher->amount }}{{ $voucher->amount_type == 'PERCENTAGE' ? '%' : '' }}
                            </td>
                            <td class="text-center">{!! $voucher->is_active ? '<i class="far fa-fw fa-check text-success"></i>' : '<i class="fal fa-fw fa-times text-danger"></i>' !!}</td>
                            <td class="text-right">
                                <div class="list-icons">
                                    <a href="{{ route('tenant.vouchers.show', [ 'id' => $voucher->id ]) }}"
                                        class="list-icons-item text-primary"><i class="icon-pencil7"></i></a>
                                    @if (Auth::user()->role_id == 1)
                                    <a href="{{ route('tenant.vouchers.delete', [ 'id' => $voucher->id ]) }}"
                                        class="list-icons-item text-danger confirm-dialog"
                                        data-text="Delete voucher?"><i class="icon-trash"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>

@endsection
