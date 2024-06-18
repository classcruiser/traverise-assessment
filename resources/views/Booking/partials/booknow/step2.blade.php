<div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">2 - Accommodation</div>
@if ($step == 2)
<div class="section-body {{ $step == 2 ? 'active' : '' }}">

    <select name="room_id" class="form-control" id="location-select">
        <option value="">Select room category</option>
        @foreach ($rooms as $room)
            <option value="{{ $room->id }}">{{ $room->name }}</option>
        @endforeach
    </select>

    @foreach ($rooms as $room)
        <form action="/book-now/rooms" method="post">
            <div class="cover-image cover-{{ $room->id }}">
                <div class="mt-3 position-relative">
                    <div class="position-absolute bottom-0 left-0 w-100 cover-title p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ $room->name }}</span>
                            @if (!$room->allow_pending && $availability[$room->id]['availability_status'] !== 1)
                                <span class="text-danger">SOLD OUT</span>
                            @else
                                <span id="room-{{ $room->id }}-price">&euro;{{ number_format($availability[$room->id]['price'], 0) }}</span>
                            @endif
                        </div>
                    </div>
                    <section class="splide" aria-label="{{ $room->name }} gallery">
                      <div class="splide__track">
                            <ul class="splide__list">
                                @if (@file_exists(public_path('tenancy/assets/images/rooms/'. $room->id .'/'. $room->featured_image)))
                                    <li class="splide__slide"><img src="{{ asset('images/rooms/'. $room->id .'/'. $room->featured_image) }}" alt="" /></li>
                                @endif
                                @foreach ($room->gallery as $path)
                                    <li class="splide__slide"><img src="{{ asset('images/rooms/'. $room->id .'/'. $path) }}" alt="" /></li>
                                @endforeach
                            </ul>
                      </div>
                    </section>
                </div>
                <div class="py-3">
                    @if (!$room->allow_pending && $availability[$room->id]['availability_status'] !== 1)
                    @else
                        <div class="d-flex justify-content-between align-items-start">
                            <p class="text-uppercase">
                                Accommodation:
                                <b>
                                    <span class="room-accommodation-{{ $room->id }}">
                                        @if ($room->room_type == 'Private')
                                            {{ $room->room_type }}
                                        @else
                                            @if ($guest <= 1)
                                                SHARED WITH OTHER GUESTS
                                            @else
                                                {{ $room->room_type }}
                                            @endif
                                        @endif
                                    </span>
                                </b>
                            </p>
                            <div id="room-{{ $room->id }}-availability">
                                @if ($availability[$room->id]['availability_status'] == 1)
                                    <b class="text-success">ROOM IS AVAILABLE</b>
                                @elseif ($availability[$room->id]['availability_status'] == 2)
                                    <b class="text-orange">LIMITED AVAILABILITY</b>
                                @elseif ($availability[$room->id]['availability_status'] == 3)
                                    <b class="text-danger">FULL</b>
                                @endif
                            </div>
                        </div>
                        <div class="border-top-1 border-alpha-grey py-3 my-2 border-bottom-1">
                            {!! nl2br($room->room_short_description) !!}
                        </div>
                        <div class="{{ ($room->location->id != 3 && $room->location->id != 4) && ($room->allow_private && $room->room_type != 'Private' && $availability[$room->id]['open_spot'] >= 1) ? 'pt-1 pb-2' : '' }}">

                            @if ($room->allow_private && $room->room_type != 'Private' && $availability[$room->id]['open_spot'] >= 1)
                                <div class="private-booking private-{{ $room->id }} {{ $guest <= 1 ? 'private-booking-active' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div style="width: 100px;" class="mr-2"><b>Private</b></div>
                                        <div style="flex-grow: 1" id="private-{{ $room->id }}-container">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" name="private_booking" class="form-check-input-styled mr-1 book-as-private book-private-{{ $room->id }}" data-id="{{ $room->id }}" data-allow-private="{{ $room->allow_private }}" data-step="2" data-guest="{{ $guest }}" data-room-type="{{ $room->room_type }}" data-fouc />
                                                    Book as private
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <input type="hidden" name="guest" value="{{ $guest }}" />

                            @if (count($room->bed_types()) > 1 && $room->room_type != 'Private')
                                <div class="bed-type bed-type-{{ $room->id }} {{ $guest > 1 ? 'bed-select-active' : '' }} mt-2">
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div style="width: 100px;" class="mr-2"><b>Bed type</b></div>
                                        <div style="flex-grow: 1">
                                            <select name="bed_type" class="form-control bed_type bed-type-{{ $room->id }}">
                                                @foreach ($room->bed_types() as $bed)
                                                    <option value="{{ $bed }}">{{ $bed }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="bed_type" value="Single" class="bed_type" />
                            @endif

                            @if ($room->room_type == 'Private' && $guest <= 1 && $room->allow_private)
                                <div class="private-booking private-booking-active">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div style="width: 100px;" class="mr-2"><b>Private</b></div>
                                        <div style="flex-grow: 1" id="private-{{ $room->id }}-container">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" readonly checked disabled name="private_booking" class="form-check-input-styled mr-1 book-as-private book-private-{{ $room->id }}" data-id="{{ $room->id }}" data-allow-private="{{ $room->allow_private }}" data-step="2" data-fouc />
                                                    Book as private
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="private_booking" value="On" />
                            @endif

                        </div>
                        <div class="{{ ($room->allow_private && $room->room_type != 'Private' && $availability[$room->id]['open_spot'] >= 1) ? 'border-top-1 mt-2' : '' }} border-alpha-grey py-3 border-bottom-1 d-flex justify-content-end">
                            @csrf
                            @if ($room->max_guest && $room->max_guest < $guest)
                                <div class="alert alert-warning w-full">Sorry! {{ $room->name }} can only be booked for {{ $room->max_guest }} {{ \Illuminate\Support\Str::plural('guest', $room->max_guest )}}.</div>
                            @elseif ($room->min_guest && $room->min_guest > $guest)
                                <div class="alert alert-warning w-full">Sorry! {{ $room->name }} can only be booked for {{ $room->min_guest }} {{ \Illuminate\Support\Str::plural('guest', $room->min_guest )}}.</div>
                            @elseif ($room->min_nights && $room->min_nights > $dates['duration'])
                                <div class="alert alert-warning w-full">Sorry! {{ $room->name }} can only be booked for a minimum nights of {{ $room->min_nights }}. Please update your date selection.</div>
                            @elseif ($room->max_nights && $room->max_nights < $dates['duration'])
                                <div class="alert alert-warning w-full">Sorry! {{ $room->name }} can only be booked for a maximum nights of {{ $room->max_nights }}. Please update your date selection.</div>
                            @else
                                <input type="hidden" name="room_id" value="{{ $room->id }}" />
                                <input type="hidden" name="room_type" value="{{ $room->room_type }}" />
                                <button class="btn btn-custom text-uppercase font-size-lg btn-lg">ADD ROOM</button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </form>
    @endforeach

    <div class="d-flex justify-content-between mt-3">
        <a class="btn bg-grey text-uppercase font-size-lg btn-lg" href="/book-now">BACK</a>
        @if (session()->has('booking_rooms') && count(session('booking_rooms') > 0))
            <a class="btn btn-custom text-uppercase font-size-lg btn-lg" href="/book-now/extras">NEXT STEP</a>
        @endif
    </div>

</div>
@section('scripts')
<script>
    window.ARRIVAL_RULE = 1;
    window.ARRIVAL_DATES = ["21 Aug 2023","28 Aug 2023"];
    window.ARRIVAL_OPTION = 'period';
</script>
    <script>
        $('#location-select').val('');
        let lastClickedDate;
        let datepicker = new HotelDatepicker(document.getElementById('datepicker'), {
            format: 'DD MMM YYYY',
            startDate: '{{ $start_date }}',
            @if ($has_rule && $disable_check_in_dates == '' && $disable_check_out_dates == '')
                endDate: '{{ $end_date }}',
            @endif
            @if ($disable_check_in_dates != '')
                noCheckInDates: [
                    '{!! ($disable_check_in_dates) !!}'
                ],
            @endif
            @if ($disable_check_out_dates != '')
                noCheckOutDates: [
                    '{!! ($disable_check_out_dates) !!}'
                ],
            @endif
            onDayClick: function (e) {
                let time = parseInt($(e).attr('time'));
                let diff = 86400000;

                if (lastClickedDate == time) {
                    //$('.datepicker__month-day').removeClass('pointer-events-none');
                }

                if (lastClickedDate !== time) {
                    lastClickedDate = time;
                }
            }
        });
    </script>
@endsection
@endif
