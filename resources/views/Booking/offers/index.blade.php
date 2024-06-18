@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
  <div class="d-flex">
    <div class="breadcrumb">
      <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <span class="breadcrumb-item">Settings</span>
      <span class="breadcrumb-item active">Special Offers</span>
    </div>

    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
  </div>
</div>

<div class="page-content">
  <div class="content-wrapper container">
    <div class="content">
      <div class="card">
        <div class="card-header header-elements-inline">
          <h4 class="card-title">Special Offers</h4>
          @can ('add setting')
            <div class="header-elements">
              <a href="{{ route('tenant.special-offers.create') }}" title="" class="btn bg-danger">
                <i class="far fa-plus mr-1"></i> New Special Offer
              </a>
            </div>
          @endcan
        </div>
        <table class="table table-xs table-compact">
          <thead>
            <tr class="bg-grey-700">
              <th class="two wide">Name</th>
              <th class="one wide text-center">Discount</th>
              <th class="two wide">Conditions</th>
              <th class="two wide">Locations/Rooms</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($offers as $offer)
              <tr>
                <td class="vertical-top"><a href="{{ route('tenant.special-offers.show', [ 'id' => $offer->id ]) }}" class="list-icons-item text-danger"><b>{{ $offer->name }}</b></a></td>
                <td class="text-center">{!! $offer->discount_type == 'Percent' ? $offer->discount_value .'%' : '&euro;'. $offer->discount_value !!}</td>
                <td>
                  @if ($offer->stay_value)
                    {{ $offer->stay_type }} {{ $offer->stay_value_readable }}<br />
                  @endif
                  @if ($offer->booked_between)
                    booked between {{ $offer->booked_between_readable }}<br />
                  @endif
                </td>
                <td>{!! $offer['location_details'] !!}</td>
                <td class="text-right">
                  <div class="list-icons">
                    <a href="{{ route('tenant.special-offers.show', [ 'id' => $offer->id ]) }}" class="list-icons-item text-grey"><i class="icon-pencil7"></i></a>
                    @can ('delete setting')
                      <a href="{{ route('tenant.special-offers.remove', [ 'id' => $offer->id ]) }}" class="list-icons-item text-danger confirm-dialog" data-text="Delete this offer?"><i class="icon-trash"></i></a>
                    @endcan
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