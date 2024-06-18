@extends('Booking.app')

@section('content')
<div class="custom-container">
  <div class="main edit-booking">
    <h1>#{{ $booking->ref }} - Edit</h1>
    <div class="ui large breadcrumb">
      <a class="section" href="{{ route('tenant.dashboard') }}">Dashboard</a>
      <i class="right angle icon divider"></i>
      @if ($booking->status == 'DRAFT')
        <a class="section" href="/bookings/draft">Draft Bookings</a>
      @else
        <a class="section" href="{{ route('tenant.bookings') }}">Bookings</a>
      @endif
      <i class="right angle icon divider"></i>
      <a href="/bookings/{{ $booking->ref }}" title="" class="ection">#{{ $booking->ref }}</a>
      <i class="right angle icon divider"></i>
      <a href="/bookings/{{ $booking->ref }}/edit" title="" class="active section">Edit</a>
    </div>

    <div class="ui divider"></div>

    <div class="ui grid">
      <div class="five wide column">
        <div class="booking-edit-wrap">
          <div class="title-bar">
            <h3>Details</h3>
            <div class="title-toolbar">
              <div class="ui {{ $booking->status }} tiny horizontal label">{{ $booking->status }}</div>
            </div>
          </div>
          <table class="booking-form">
            <tr>
              <td>Location</td>
              <td>{{ $booking->location_id ? $booking->location->name : '--' }}</td>
            </tr>
            <tr>
              <td>Stay</td>
              <td>{{ $booking->check_in ? $booking->check_in->format('d.m.Y') .' - '. $booking->check_out->format('d.m.Y') : '--' }}</td>
            </tr>
            <tr>
              <td>Guests</td>
              <td>{{ $booking->guests->count() > 0 ? $booking->guests->count() : '--' }}</td>
            </tr>
            <tr>
              <td>Expires after</td>
              <td>
                @if ($booking->status == 'DRAFT')
                  <div class="ui tiny form">
                    <div class="inline fields">
                      <div class="field">
                        <div class="ui selection dropdown">
                          <input type="hidden" name="expire_at" value="24H">
                          <i class="dropdown icon"></i>
                          <div class="text">24H</div>
                          <div class="menu">
                            <div class="item" data-value="24H">24H</div>
                            <div class="item" data-value="48H">48H</div>
                            <div class="item" data-value="72H">72H</div>
                          </div>
                        </div>
                      </div>
                      <div class="field">
                        at {{ $booking->expiry->format('d.m.Y H:i:s') }}
                      </div>
                    </div>
                  </div>
                @else
                --
                @endif
              </td>
            </tr>
            <tr>
              <td colspan="2"><div class="ui divider"></div></td>
            </tr>
            <tr>
              <td colspan="2">
                <div class="title-bar">
                  <h3>Main Guest {!! (!$booking->guest) ? '<span><i class="fa fa-exclamation-triangle"></i></span>' : '<span><i class="fa fa-check is-green"></i></span>' !!}</h3>
                  @if (!$booking->guest)
                    <div class="title-toolbar">
                      <div class="ui small icon buttons">
                        <a href="/bookings/{{ $booking->ref }}/new-guest" class="ui button"><i class="far fa-plus"></i> Add</a>
                      </div>
                    </div>
                  @else
                    <div class="title-toolbar">
                      <div class="ui small icon buttons">
                        <a href="/bookings/{{ $booking->ref }}/new-guest" class="ui button"><i class="fa fa-edit"></i> Edit</a>
                      </div>
                    </div>
                  @endif
                </div>
              </td>
            </tr>
            <tr>
              <td>Full Name</td>
              <td>{{ $booking->guest ? $booking->guest->details->full_name : '--' }}</td>
            </tr>
            <tr>
              <td>Company</td>
              <td>{{ $booking->guest ? $booking->guest->details->company : '--' }}</td>
            </tr>
            <tr>
              <td>Email</td>
              <td>{{ $booking->guest ? $booking->guest->details->email : '--' }}</td>
            </tr>
            <tr>
              <td>Phone</td>
              <td>{{ $booking->guest ? ($booking->guest->details->phone != '' ? $booking->guest->details->phone : '--') : '--' }}</td>
            </tr>
            <tr>
              <td>Room</td>
              <td>
                <a href="#" title="">Add Room</a>
              </td>
            </tr>
            @if ($booking->guest && $booking->guest->rooms)
              @foreach ($booking->guest->rooms as $room_info)
                <tr class="room-info">
                  <td><i class="far fa-bed"></i> {{ $room_info->room->subroom->name }}</td>
                  <td>{{ date('d.m.Y', strtotime($room_info->room->from)) }} <i class="fal fa-arrow-right"></i> {{ date('d.m.Y', strtotime($room_info->room->to)) }}</td>
                </tr>
              @endforeach
            @endif
            <tr>
              <td colspan="2"><div class="ui divider"></div></td>
            </tr>
            <tr>
              <td colspan="2">
                <div class="title-bar mb-0">
                  <h3>Other Guests</h3>
                  <div class="title-toolbar">
                    <div class="ui small icon buttons">
                      <a href="/bookings/{{ $booking->ref }}/new-guest" class="ui button"><i class="far fa-plus"></i> Add</a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            @if ($booking->other_guests)
              @foreach ($booking->other_guests as $og)
                <tr class="other-guests-row">
                  <td>&middot; <b>{{ $og->details->full_name }}</b></td>
                  <td>{{ $og->details->email }} [<a href="#" title="">edit</a> | <a href="#" title="">Add Room</a>]</td>
                </tr>
                @if ($og->rooms)
                  @foreach ($og->rooms as $room_info)
                    <tr class="room-info">
                      <td><i class="far fa-bed"></i> {{ $room_info->room->subroom->name }}</td>
                      <td>{{ date('d.m.Y', strtotime($room_info->room->from)) }} <i class="fal fa-arrow-right"></i> {{ date('d.m.Y', strtotime($room_info->room->to)) }}</td>
                    </tr>
                  @endforeach
                @endif
              @endforeach
            @endif
            <tr>
              <td colspan="2"><div class="ui divider"></div></td>
            </tr>
            <tr>
              <td colspan="2">
                <div class="title-bar mb-0">
                  <h3>
                    {{ $booking->rooms->count() }} Rooms
                    {!! $booking->rooms->count() <= 0 ? '<i class="fa fa-exclamation-triangle is-red"></i>' : '<span><i class="fa fa-check is-green"></i></span>' !!}
                  </h3>
                </div>
                @if ($booking->rooms->count())
                  <div class="room-list">
                    @foreach ($booking->rooms as $r)
                      <div class="room-list-row">
                        <span>{{ $r->subroom->name }}</span>
                        <span>{{ date('d.m.Y', strtotime($r->from)) }} <i class="fal fa-arrow-right"></i> {{ date('d.m.Y', strtotime($r->to)) }}</span>
                        <div class="ui input"><input type="text" value="{{ $r->price }}" /></div>
                      </div>
                    @endforeach
                  </div>
                @endif
              </td>
            </tr>
            <tr>
              <td colspan="2"><div class="ui divider"></div></td>
            </tr>
          </table>
        </div>
      </div>

      <div class="eleven wide column">

        <!-- separate to partials later -->
        @if ($view == 'new-guest')
          @if ($booking->guests->count() <= 0)
            <div class="ui icon message">
              <div class="ui disabled inverted dimmer">
                <div class="ui small loader"></div>
              </div>
              <i class="search icon"></i>
              <div class="content">
                <div class="header">
                  You have no guest for this booking yet.
                </div>
                <p>Add or assign existing guest.</p>
              </div>
            </div>
          @endif
          <div class="ui form">
            <div class="fields">
              <div class="six wide field">
                <div class="ui small search">
                  <div class="ui icon input">
                    <input class="prompt search-guest" type="text" placeholder="Search guest...">
                    <i class="search icon"></i>
                  </div>
                  <div class="results"></div>
                </div>
              </div>
              <button class="ui icon blue button" id="guest-search">
                <i class="plus icon"></i>
                Add
              </button>
              <input type="hidden" name="ref" value="{{ $booking->ref }}" />
            </div>
          </div>
          <div class="ui divider"></div>
        @endif

        @if ($view == 'edit')
          <div class="ui top attached tabular menu">
            <a class="item active" data-tab="first"><i class="fal fa-file-alt"></i> &nbsp; Invoice</a>
            <a class="item" data-tab="second"><i class="fal fa-tags"></i> &nbsp; Discounts</a>
            <a class="item" data-tab="third"><i class="fal fa-money-bill"></i> &nbsp; Price</a>
          </div>
          <div class="ui bottom attached tab segment active" data-tab="first">
            @include('Booking.bookings.tab-guests')
          </div>
          <div class="ui bottom attached tab segment" data-tab="second">
            Second
          </div>
          <div class="ui bottom attached tab segment" data-tab="third">
            Third
          </div>
        @endif
        <!-- end -->

      </div>
    </div>

  </div>
</div>

@endsection

@section('scripts')
<script>
var ref = '{{ $booking->ref }}';
$('.ui.search').search({
  apiSettings: {
    url: '/guests/quick-search?q={query}&ref='+ ref
  },
  minCharacters: 3
});
tippy('.tippy', {
  content: 'Tooltip',
  arrow: true,
})
</script>
@endsection
