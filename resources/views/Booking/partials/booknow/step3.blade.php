<div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">3 - Add-ons</div>
@if($step == 3)
    <div class="section-body {{$step == 3 ? 'active' : ''}}">

        <?php $room = $rooms->where('id', $booking_room['room_id'])->first(); ?>
        <form action="/book-now/extras" method="post">
            <input type="hidden" name="origin" class="addon-origin" value="book-now" />
            <div class="cover-image cover-{{$room->id}} cover-active">
                <div class="mt-3 position-relative">
                    <div class="position-absolute bottom-0 left-0 w-100 cover-title p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{$room->name}}</span>
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
                    <div class="d-flex justify-content-between align-items-start">
                        <p class="text-uppercase">
                            Accommodation:
                            <b>
                                <span class="room-accommodation-{{$room->id}}">
                                    @if($room->room_type == 'Private')
                                        {{$room->room_type}}
                                    @else
                                        @if($guest <= 1)
                                            {{session('room')['private_booking'] ? 'PRIVATE' : 'SHARED WITH OTHER GUESTS'}}
                                        @else
                                            {{$room->room_type}}
                                        @endif
                                    @endif
                                </span>
                            </b>
                        </p>
                        @if($booking_room['availability_status'] == 1)
                            <b class="text-success">ROOM IS AVAILABLE</b>
                        @elseif($booking_room['availability_status'] == 2)
                            <b class="text-orange">LIMITED AVAILABILITY</b>
                        @elseif($booking_room['availability_status'] == 3)
                            <b class="text-danger">FULL</b>
                        @endif
                    </div>
                    <div class="border-top-1 border-alpha-grey py-3 my-2 border-bottom-1">
                        {!! nl2br($room->room_short_description) !!}
                    </div>
                    <div class="pt-1 pb-2">
                        @if($room->allow_private && $room->room_type != 'Private' && $booking_room['open_spot'] >= 1)
                            <div class="private-booking private-{{$room->id}} {{$guest <= 1 ? 'private-booking-active' : ''}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div style="width: 100px;" class="mr-2"><b>Private</b></div>
                                    <div style="flex-grow: 1" id="private-{{$room->id}}-container">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" name="private_booking" class="form-check-input-styled mr-1 book-as-private book-private-{{$room->id}}" data-id="{{$room->id}}" data-allow-private="{{$room->allow_private}}" data-surcharge="{{$room->empty_fee_low}}" data-step="3" data-fouc {{session('room')['private_booking'] ? 'checked' : ''}} />
                                                Book as private
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <input type="hidden" name="guest" value="{{$guest}}" />

                        @if(count($room->bed_types()) > 1 && $room->room_type != 'Private')
                            <div class="bed-type bed-type-{{$room->id}} {{$guest > 1 || session('room')['private_booking'] ? 'bed-select-active' : ''}} mt-2">
                                @if($guest > 1)
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div style="width: 100px;" class="mr-2"><b>Bed type</b></div>
                                        <div style="flex-grow: 1">
                                            <select name="bed_type" class="form-control bed_type bed-type-{{$room->id}}">
                                                @foreach($room->bed_types() as $bed)
                                                    <option value="{{$bed}}" {{session('room.bed_type') == $bed ? 'selected' : ''}}>{{$bed}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <hr class="border-alpha-grey" />
                        @else
                            <input type="hidden" name="bed_type" value="Single" class="bed_type" />
                        @endif

                        @if($room->room_type == 'Private' && $guest <= 1 && $booking_room['open_spot'] >= 1 && $room->allow_private)
                            <div class="private-booking private-booking-active border-bottom-1 border-alpha-grey pb-3 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div style="width: 100px;" class="mr-2"><b>Private</b></div>
                                    <div style="flex-grow: 1" id="private-{{$room->id}}-container">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" readonly checked disabled name="private_booking" class="form-check-input-styled mr-1 book-as-private book-private-{{$room->id}}" data-id="{{$room->id}}" data-allow-private="{{$room->allow_private}}" data-step="2" data-fouc />
                                                Book as private
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                    @if (count($addons) || count($transfers))
                        <div class="pt-1 pb-2">
                            <h4><b>Available Add-ons</b></h4>

                            @foreach($addons as $addon)
                                <div class="d-flex justify-content-start align-items-start py-3">
                                    <div class="mr-4 mobile-hide">
                                        <img src="{{asset('images/addons/'. tenant('id') .'_addon_'. $addon->id .'.jpg?'. date('Ymd'))}}" class="rounded d-block" style="width: 100px" />
                                    </div>
                                    <div class="w-75 mr-4">
                                        <div class="text-uppercase font-size-lg">
                                            <b>{{$addon->name}}</b>
                                        </div>
                                        <div class="text-muted mb-2">
                                            <span class="price-{{$addon->id}}">
                                                @if ($booking_room['addons']->contains('id', $addon->id))
                                                    &euro;{{collect($booking_room['addons'])->where('id', $addon->id)->first()['total']}}
                                                @else
                                                    &euro;{{$addon->total}}</b>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-block w-full">
                                                <label>Guest:</label>
                                                <select name="addon[{{$addon->id}}][guest]" class="form-control mr-1 addon-guest addon-guest-{{$addon->id}} addon-select-{{$addon->id}}" {{$booking_room['addons']->contains('id', $addon->id) ? 'disabled' : ''}} data-id="{{$addon->id}}">
                                                    @for($i = 1; $i <= ($addon->max_units ?? $booking_room['guest']); $i++)
                                                        <option value="{{$i}}"
                                                            @if ($booking_room['addons']->contains('id', $addon->id))
                                                                {{collect($booking_room['addons'])->where('id', $addon->id)->first()['guests'] == $i ? 'selected' : ''}}
                                                            @endif
                                                        >
                                                            {{$i}} {{Str::plural($addon->unit_name, $i)}}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>

                                            @if ($addon->week_question)
                                                <div class="w-full">
                                                    <label>Please select week:</label>
                                                    <select name="addon[{{$addon->id}}][weeks]" class="form-control ml-1 addon-weeks addon-weeks-{{$addon->id}} addon-select-{{$addon->id}}" data-id="{{$addon->id}}">
                                                        @for ($i = 1; $i <= $weeks; $i++)
                                                            <option value="{{ $i }}">
                                                                {{ (new NumberFormatter('en_US', NumberFormatter::ORDINAL))->format(intVal($i)) }} week
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            @endif

                                            @if($addon->rate_type == 'Day')
                                                <div class="w-full">
                                                    <label>Days:</label>
                                                    <select name="addon[{{$addon->id}}][duration]" class="form-control ml-1 addon-duration addon-duration-{{$addon->id}} addon-select-{{$addon->id}}" {{$booking_room['addons']->contains('id', $addon->id) ? 'disabled' : ''}} data-id="{{$addon->id}}">
                                                        @for($i = 1; $i <= (($addon->max_stay && $addon->max_stay <= $duration) ? $addon->max_stay : $duration); $i++)
                                                            <option
                                                                value="{{$i}}"
                                                                @if ($booking_room['addons']->contains('id', $addon->id))
                                                                    {{collect($booking_room['addons'])->where('id', $addon->id)->first()['amount'] == $i ? 'selected' : ''}}
                                                                @else
                                                                    {{$duration == $i ? 'selected' : ''}}
                                                                @endif
                                                            >
                                                                {{$i}} {{Str::plural('day', $i)}}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-2">
                                            <p class="mb-0">{{$addon->description}}</p>
                                        </div>
                                    </div>
                                    <div class="form-check ml-auto {{(!$addon->page_link || $addon->page_link == '') ? 'align-self-center' : 'align-self-end'}} pb-2 h-100">
                                        <label class="form-check-label">
                                            <input type="checkbox" id="addon_{{$addon->id}}" name="addon[{{$addon->id}}]" class="form-check-input-styled mr-1 addon-check" data-fouc {{$booking_room['addons']->contains('id', $addon->id) ? 'checked' : ''}} data-id="{{$addon->id}}" />
                                        </label>
                                    </div>
                                </div>
                            @endforeach

                            @foreach($transfers as $transfer)
                                <div class="d-flex justify-content-start align-items-start py-3">
                                    <div class="mr-4 mobile-hide">
                                        <img src="{{asset('images/transfers/'. tenant('id') .'_transfer_'. $transfer->id .'.jpg?'. date('Ymd'))}}" class="rounded d-block" style="width: 100px" />
                                    </div>

                                    <div class="w-75 mr-4">
                                        <div class="text-uppercase font-size-lg"><b>{{$transfer->name}}</b></div>
                                        <div class="text-muted">
                                            {!! $transfer->price > 0 ? '<span class="transfer-price-'. $transfer->id .'">&euro;'. $transfer->price .'</span>' : '<span class="text-success"><b>FREE</b></span>' !!}
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <select name="transfer[{{$transfer->id}}][guest]" class="form-control mr-1 transfer-guest transfer-guest-{{$transfer->id}} transfer-select-{{$transfer->id}}" {{$booking_room['transfers']->contains('id', $transfer->id) ? 'disabled' : ''}} data-id="{{$transfer->id}}">
                                                @for($i = 1; $i <= $booking_room['guest']; $i++)
                                                    <option value="{{$i}}"
                                                        @if (in_array($transfer->id, $booking_room['transfers_key']))
                                                            {{collect($booking_room['transfers'])->where('id', $transfer->id)->first()['guests'] == $i ? 'selected' : ''}}
                                                        @else
                                                            {{$booking_room['guest'] == $i ? 'selected' : ''}}
                                                        @endif
                                                    >
                                                        {{$i}} {{Str::plural('guest', $i)}}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-check ml-auto align-self-center">
                                        <label class="form-check-label">
                                            <input type="checkbox" id="transfer_{{$transfer->id}}" name="transfer[{{$transfer->id}}]" class="form-check-input-styled mr-1 transfer-check" data-fouc {{$booking_room['transfers']->contains('id', $transfer->id) ? 'checked' : ''}} data-id="{{$transfer->id}}" />
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-between {{ (count($addons) || count($transfers)) ? 'mt-3' : '' }}">
                <a class="btn bg-grey text-uppercase font-size-lg btn-lg" href="/book-now/rooms{{$ga}}">BACK</a>
                <a class="btn btn-custom text-uppercase font-size-lg btn-lg" href="/book-now/details{{$ga}}">NEXT STEP</a>
            </div>
        </form>
    </div>
@endif
