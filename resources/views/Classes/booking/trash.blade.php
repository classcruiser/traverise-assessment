@extends('Booking.app')

@section('content')
    <x-alert-error />
    <x-alert-message />

    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
                <a href="{{ route('tenant.classes.bookings.index') }}" class="breadcrumb-item">Classes</a>
                <span class="breadcrumb-item active">Deleted Bookings</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper">
            <div class="content">
                <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                    <h4 class="m-0 mr-auto"><i class="fa fa-fw fa-trash mr-1"></i> Deleted Bookings</h4>
                    <button class="btn btn-labeled btn-labeled-left bg-orange-400 ml-1 collapsed" data-toggle="collapse" href="#advanced-search">
                        <b><i class="icon-search4"></i></b> Advanced Search
                    </button>
                </div>
                @include('Classes.partials.bookings.advanced-search-trash')

                <div class="card">
                    <table class="table table-xs table-compact dark">
                        <thead>
                            <tr class="bg-grey-700">
                                <th class="">Ref</th>
                                <th>Guest</th>
                                <th class="text-center">Session</th>
                                <th class="text-center">People</th>
                                <th class="text-right">Price</th>
                                <th class="text-right">Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        <a href="{{ route('tenant.classes.bookings.show', ['ref' => $booking->ref]) }}" class="text-danger"><b>{{$booking->ref}}</b></a>
                                    </td>
                                    <td>
                                        @if ($booking->guest)
                                            <a href="{{ route('tenant.classes.guests.show', [ 'id' => $booking->guest->details->id ]) }}" title="" class="text-danger"><b>{{$booking->guest->details->full_name}}</b></a>
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $booking->sessions()->count() }}
                                    </td>
                                    <td class="text-center">{{ $booking->people() }}</td>
                                    <td class="text-right"><b>&euro;{{floatVal(round($booking->payment->total, 2))}}</b></td>
                                    <td class="text-right">
                                        {!! $booking->histories->where('action', 'Abandon booking')->first()?->details !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>{{$bookings->appends($_GET)->links()}}</div>
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
    });
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
