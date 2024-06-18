<h4>Main Guest</h4>
@if ($booking->guest)
    @if ($booking->guest->rooms)
        @foreach ($booking->guest->rooms as $room_info)
            <tr class="room-info">
                <td><i class="far fa-bed"></i> {{ $room_info->room->subroom->name }}</td>
                <td>{{ date('d.m.Y', strtotime($room_info->room->from)) }} <i class="fal fa-arrow-right"></i> {{ date('d.m.Y', strtotime($room_info->room->to)) }}</td>
            </tr>
        @endforeach
    @endif
@endif