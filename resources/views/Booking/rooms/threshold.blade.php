@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
  <div class="d-flex">
    <div class="breadcrumb">
      <a href="/dashboard" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <span class="breadcrumb-item">Settings</span>
      <span class="breadcrumb-item active">Rooms Threshold</span>
    </div>

    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
  </div>
</div>

<div class="page-content">
  <div class="content-wrapper container no-overflow">
    <div class="content">
      <div class="mb-2 py-1 px-2 d-flex justify-content-between align-items-center">
        <h4 class="m-0 mr-auto"><i class="fal fa-fw fa-percent mr-1"></i> Rooms Threshold</h4>
        <button class="btn bg-danger room-threshold">UPDATE</button>
      </div>

      <div class="card">
        <form action="javascript:" method="post" id="threshold-form">
          @csrf
          <table class="table table-xs table-compact">
            <thead>
              <tr class="bg-grey-700">
                <th>Name</th>
                <th class="text-center">Active</th>
                <th>Location</th>
                <th>Rooms count</th>
                <th>Beds count</th>
                <th class="text-right">Threshold</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($rooms as $room)
                <tr>
                  <td class="vertical-top"><b><a href="{{ route('tenant.rooms.show', [ 'id' => $room->id ]) }}#room-details" class="list-icons-item text-danger">{{ $room->name }}</a></b></td>
                  <td class="text-center">{!! $room->active ? '<i class="far fa-fw fa-check text-success"></i>' : '<i class="fal fa-fw fa-times text-danger"></i>' !!}</td>
                  <td>{{ $room->location->name }}</td>
                  <td>{{ $room->total_rooms }} {{ Str::plural('Room', $room->total_rooms) }}</td>
                  <td>{{ $room->total_capacity }} {{ Str::plural('Bed', $room->total_capacity) }}</td>
                  <td class="text-right">
                    <div class="d-flex justify-content-end">
                      <div class="input-group input-group-sm" style="width: 90px">
                        <input type="text" name="threshold[{{ $room->id }}]" class="form-control form-control-sm" value="{{ $room->limited_threshold }}" {{ (Auth::user()->role == 'MASTER') ? '' : 'readonly' }}/>
                        <input type="hidden" name="old_threshold[{{ $room->id }}]" value="{{ $room->limited_threshold }}" />
                        <input type="hidden" name="room_name[{{ $room->id }}]" value="{{ $room->name }}" />
                        <span class="input-group-append">
                          <span class="input-group-text">%</span>
                        </span>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
              <tr>
                <td colspan="6" class="text-right attach-bottom">
                  <button class="btn bg-danger room-threshold" type="submit">UPDATE</button>
                </td>
              </tr>
            </tbody>
          </table>
        </form>
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