@php
    use App\Enums\Tax;
@endphp
@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item active">Tax</span>
        </div>
        
        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h4 class="card-title">Tax</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.taxes.create') }}" title="" class="btn bg-danger">
                            <i class="far fa-plus mr-1"></i> New Tax
                        </a>
                    </div>
                </div>
                <table class="table table-xs table-compact sortable" data-url="{{ route('tenant.taxes.sort') }}">
                    <thead>
                        <tr class="bg-grey-700">
                            <th></th>
                            <th class="">Name</th>
                            <th>Type</th>
                            <th class="">Rate</th>
                            <th class=""><span class="tippy" data-tippy-content="Calculation Type">Cal. Type <i class="fa fa-exclamation-circle"></i></span></th>
                            <th class=""><span class="tippy" data-tippy-content="Calculation Charge">Cal. Charge <i class="fa fa-exclamation-circle"></i></span></th>
                            <th class="">
                                <x-booking.tax-inclusive>Tax Type <i class="fa fa-exclamation-circle"></i></x-booking.tax-inclusive>
                            </th>
                            <th class="text-center">Active</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($taxes as $tax)
                            <tr class="bg-white" data-id="{{ $tax->id }}">
                                <td class="text-center"><span class="handler cursor-move"><i class="fal fa-bars fa-fw"></i></span></td>
                                <td class="vertical-top"><a href="{{ route('tenant.taxes.show', $tax->id) }}" class="list-icons-item text-danger"><b>{{ $tax->name }}</b></a></td>
                                <td class="small-caps">{{ $tax->type }}</td>
                                <td class="monospace">{!! $tax->type == 'percentage' ? number_format($tax->rate, 0) .'%' : '&euro;'. $tax->rate !!}</td>
                                <td class="">{{ $tax->type != 'percentage' ? Tax::from($tax->calculation_type)->readable() : '-' }}</td>
                                <td class="">{{ $tax->type != 'percentage' ? Tax::from($tax->calculation_charge)->readable() : '-' }}</td>
                                <td class="small-caps">{{ Tax::from($tax->tax_type)->readable() }}</td>
                                <td class="text-center">
                                    <i class="far fa-fa fa-{{ $tax->is_active ? 'check text-success' : 'times text-danger' }}"></i>
                                </td>
                                <td class="text-right">
                                    <div class="list-icons">
                                        <a href="{{ route('tenant.taxes.show', $tax->id) }}" class="list-icons-item text-grey"><i class="icon-pencil7"></i></a>
                                        <a href="{{ route('tenant.taxes.delete', $tax->id) }}" class="list-icons-item text-danger confirm-dialog" data-text="Delete this tax?"><i class="icon-trash"></i></a>
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

@section('scripts')
<script>
    tippy('.tippy', {
        content: 'Tooltip',
        arrow: true,
    })
</script>
@endsection