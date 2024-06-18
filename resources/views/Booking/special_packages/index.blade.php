@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
  <div class="d-flex">
    <div class="breadcrumb">
      <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <span class="breadcrumb-item">Settings</span>
      <span class="breadcrumb-item active">Special Packages</span>
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
        <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-box-alt mr-1"></i> Special Packages</h4>
        @can ('add setting')
          <a href="{{ route('tenant.special-packages.create') }}" title="" class="btn bg-danger">
            <i class="far fa-plus mr-1"></i> New Special Package
          </a>
        @endcan
      </div>

      <div class="card">
        <table class="table table-xs table-compact">
          <thead>
            <tr class="bg-grey-700">
              <th>Name</th>
              <th>Location</th>
              <th>Stay Period</th>
              <th class="text-right">Price</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @if ($packages->count() <= 0)
              <tr>
                <td colspan="8">No Special Package yet</td>
              </tr>
            @endif
            @foreach ($packages as $package)
              <tr>
                <td class="vertical-top">
                  <b><a href="{{ route('tenant.special-packages.show', [ 'id' => $package->id ]) }}" class="list-icons-item text-danger">{{ $package->name }}</a></b>
                  <a href="book-package/{{ $package->slug }}" title="" target="_blank" class="text-danger"><i class="fal fa-link"></i></a>
                  <br />
                  <span class="text-uppercase text-grey-400 font-size-xs font-weight-bold">{{ $package->nights }} nights / {{ $package->min_guest }} guests</span>
                </td>
                <td>
                  <span class="tippy" data-tippy-content="{{ $package->room->name }}"><b>{{ $package->location->short_name }}</b></span>
                </td>
                <td>
                  <b>{{ $package->check_in->format('d.m.Y') }} - {{ $package->check_out->format('d.m.Y') }}</b>
                </td>
                <td class="text-right text-grey-500">&euro;{{ number_format($package->price) }}</td>
                <td class="text-right">
                  <div class="list-icons">
                    <a href="{{ route('tenant.special-packages.show', [ 'id' => $package->id ]) }}" class="list-icons-item text-primary"><i class="icon-pencil7"></i></a>
                    @can ('delete setting')
                      <a href="{{ route('tenant.special-packages.delete', [ 'id' => $package->id ]) }}" class="list-icons-item text-danger confirm-dialog" data-text="Delete package?"><i class="icon-trash"></i></a>
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