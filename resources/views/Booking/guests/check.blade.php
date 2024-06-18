@extends('Booking.app')

@section('content')
<div class="vh-95">
  <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
      <div class="breadcrumb">
        <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-1"></i> Home</a>
        <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item" style="text-transform: capitalize">{{ $type }}</a>
        <span class="breadcrumb-item active"># {{ $booking->ref }}</span>
      </div>

      <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
  </div>

  <div class="page-content">

    <div class="content-wrapper container">
      <div class="content">

        <h1 class="mb-0 pb-0">{{ $booking->ref }}</h1>
        <h5 class="text-muted mt-0 mb-2 text-uppercase">{{ $type }}</h5>

        <a href="{{ route('tenant.bookings.show', $booking->ref) }}" class="btn btn-danger btn-sm mb-3" title="" target="_blank">View booking</a>
        
        <form action="{{ route('tenant.guests.updateCheckGuest', [ 'type' => $type, 'ref' => $ref ]) }}" method="post">
          @foreach ($booking->guests as $guest)
            <div class="card booking-details">
              <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title text-uppercase">
                    <i class="fal fa-user fa-fw mr-1"></i> <b>{{ $guest->details->full_name }}</b>
                    <a href="{{ route('tenant.bookings.editGuest', [ 'ref' => $booking->ref, 'booking_guest_id' => $guest->id ]) }}" class="btn btn-danger btn-sm ml-1" title="" target="_blank">EDIT GUEST</a>
                </h6>
                <div class="header-elements">
                  <div class="list-icons">
                    <a class="list-icons-item rotate-180" data-action="collapse"></a>
                  </div>
                </div>
              </div>
              
              @foreach ($guest->rooms as $room)
                <div class="card-body">
                  <h5 class="mt-0 mb-1"><i class="fal fa-bed fa-fw mr-1"></i> {{ $room->room->room->name .' : '. $room->room->subroom->name }}</h5>
                  <h6 class="font-size-md mb-1"><i class="fal fa-calendar-alt fa-fw mr-1"></i> {{ $room->room->from->format('d.m.Y') }} &ndash; {{ $room->room->to->format('d.m.Y') }}</h6>
                </div>
                <div class="card-body p-0">
                  <table width="100%" class="table">
                    <tr>
                      <th width="100%">
                        <div class="d-flex justify-content-between align-items-center">
                          <span><i class="fal fa-shopping-cart fa-fw mr-1"></i> <b>ADDONS</b></span>
                          <a href="{{ route('tenant.bookings.editGuestRoom', [ 'ref' => $booking->ref, 'booking_guest_id' => $guest->id, 'roomid' => $room->booking_room_id ]) }}" class="btn btn-danger btn-sm" title="" target="_blank">EDIT</a>
                        </div>
                      </th>
                    </tr>
                    @if ($room->room->addons && $room->room->addons->count() > 0)
                      @foreach ($room->room->addons as $addon)
                        <tr>
                          <td>
                            {{ $loop->iteration }}. 
                            {{ $addon->details->name }}
                            @if ($addon->extra_id == 15)
                              <br />
                              <div class="text-primary mt-1">{{ $addon->board_info }}</div>
                            @endif
                          </td>
                        </tr>
                      @endforeach
                    @else
                      <tr>
                        <td>NO ADDON</td>
                      </tr>
                    @endif
                  </table>
                </div>
              @endforeach
            </div>
          @endforeach

        </form>
      </div>
    </div>

  </div>

</div>
@endsection
