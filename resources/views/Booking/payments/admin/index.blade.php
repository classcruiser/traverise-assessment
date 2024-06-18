@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
  <div class="d-flex">
    <div class="breadcrumb">
      <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <span class="breadcrumb-item active">Payments</span>
    </div>

    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
  </div>
</div>

<div class="page-content">
  <div class="content-wrapper">
    <div class="content">
      <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
        <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-calendar-alt mr-1"></i> All Payments</h4>
        <button class="btn btn-labeled btn-labeled-left bg-orange-400 ml-1 collapsed" data-toggle="collapse" href="#advanced-search">
          <b><i class="icon-search4"></i></b> Advanced Search
        </button>
      </div>
      <div id="advanced-search" class="collapse {{ request()->has('ref') ? 'show' : '' }}">
        <div class="px-3 d-flex justify-content-end align-items-start mb-3">
          <form action="{{ route('tenant.payments') }}" method="get">
            <div style="width: 680px;" class="p-3 border-1 border-alpha-grey">

              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Booking reference number</label>
                    <input type="text" name="ref" class="form-control form-control-sm" placeholder="Booking reference number" value="{{ request('ref') }}" />
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Guest name</label>
                    <input type="text" name="guest_name" class="form-control form-control-sm" placeholder="Guest name" value="{{ request('guest_name') }}"/>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Email address</label>
                    <input type="text" name="email" class="form-control form-control-sm" placeholder="Email address" value="{{ request('email') }}"/>
                  </div>
                </div>
                <div class="col-sm-4">
                  <label>Methods</label>
                  <select class="form-control form-control-sm" name="method">
                    <option value="">All</option>
                    <option value="banktransfer" {{ request('method') == 'banktransfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="paypal" {{ request('method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                    <option value="stripe" {{ request('method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                    <option value="transferwise" {{ request('method') == 'transferwise' ? 'selected' : '' }}>Transferwise</option>
                  </select>
                </div>
                <div class="col-sm-8">
                  <label>Payment dates</label>
                  <input type="text" class="form-control daterange-empty" name="dates" value="{{ request('dates') }}" placeholder="select dates" autocomplete="off" />
                </div>
                <div class="col-sm-4">
                  <div class="form-group mb-0">
                    <label>&nbsp;</label>
                    <a href="{{ route('tenant.payments') }}" title="" class="btn bg-grey btn-sm d-block">Reset search</a>
                  </div>
                </div>
                <div class="col-sm-8">
                  <div class="form-group mb-0">
                    <label>&nbsp;</label>
                    {!! csrf_field() !!}
                    <button class="btn d-block w-100 bg-danger btn-sm">Search</button>
                  </div>
                </div>
              </div>

            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <table class="table table-xs table-compact dark">
          <thead>
            <tr class="bg-grey-700">
              <th class="two wide">Ref</th>
              <th>Guest</th>
              <th>Camp</th>
              <th>Stay</th>
              <th class="text-center">Methods</th>
              <th>Bank Info</th>
              <th>Paid At</th>
              <th>Verified By</th>
              <th>Verified At</th>
              <th class="text-right">Amount</th>
              <th class="text-right">Total</th>
              <th class="text-center">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($payments as $payment)
              <tr>
                <td>
                  <a href="{{ route('tenant.bookings.show', [ 'ref' => $payment->payment->booking->ref ]) }}" class="text-danger"><b>{{ $payment->payment->booking->ref }}</b></a>
                </td>
                <td><a href="{{ route('tenant.guests.show', [ 'id' => $payment->payment->booking->guest->details->id ]) }}" title="" class="text-danger"><b>{{ $payment->payment->booking->guest->details->full_name }}</b></a></td>
                <td>{{ $payment->payment->booking->location->short_name }}</td>
                <td>{{ $payment->payment->booking->check_in->format('d.m.Y') }} <i class="fa fa-caret-right mx-1"></i> {{ $payment->payment->booking->check_out->format('d.m.Y') }}</td>
                <td class="text-center"><span class="payment-method-badge badge-{{ $payment->methods }}">{{ $payment->methods }}</span></td>
                <td>
                  @if ($payment->bank_name != '')
                    <span
                      class="payment-bank-info tippy"
                      data-tippy-content="Acc number: {{ $payment->account_number ?? '---' }}<br />Acc Owner: {{ $payment->account_owner ?? '---' }}
                      <br />Iban Code: {{ $payment->iban_code ?? '---' }}"
                    >
                      {{ $payment->bank_name }}
                    </span>
                  @else
                    ---
                  @endif
                </td>
                <td>{{ $payment->paid_at?->format('d.m.Y') }}</td>
                <td>{{ $payment->user->name }}</td>
                <td>{{ $payment->verified_at ? $payment->verified_at->format('d.m.Y H:i:s') : '---' }}</td>
                <td class="text-right"><b>&euro;{{ $payment->payment->booking->parsePrice($payment->amount) }}</b></td>
                <td class="text-right"><b>&euro;{{ $payment->payment->booking->parsePrice($payment->payment->total) }}</b></td>
                <td class="text-center">{{ $payment->payment->status }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-between align-items-center">
        <div>{{ $payments->appends($_GET)->links() }}</div>
        <a href="{{ (request()->fullUrl()) . (request()->has('_token') ? '&' : '?') }}export=true" title="" class="btn btn-success">
          <i class="fa fa-fw fa-file-excel"></i> Export to Excel
        </a>
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
$('.daterange-empty').daterangepicker({
  autoApply: true,
  showDropdowns: true,
  minDate: "01/01/2018",
  minYear: 2018,
  maxYear: 2030,
  autoUpdateInput: false,
  locale: {
    format: 'DD.MM.YYYY'
  },
  ranges: {
    'Today': [moment(), moment()],
    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
    'This Week': [moment().startOf('week'), moment().endOf('week')],
    'Last Week': [moment().startOf('week').subtract(7, 'days'), moment().endOf('week').subtract(7, 'days')],
    'This Month': [moment().startOf('month'), moment().endOf('month')],
    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
    'This Year': [moment().startOf('year'), moment().endOf('year')],
    'Last Year': [moment().startOf('year').subtract(1, 'year'), moment().endOf('year').subtract(1, 'year')],
  },
  alwaysShowCalendars: true,
});
$('.daterange-empty').on('apply.daterangepicker', function(ev, picker) {
  $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
});

$('.daterange-empty').on('cancel.daterangepicker', function(ev, picker) {
  $(this).val('');
});
</script>
@endsection