<div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">5 - Confirm Booking</div>
@if ($step == 5)
    <div class="section-body {{ $step == 5 ? 'active' : '' }}">
        <h4><b>Accommodation</b></h4>

        <div class="cover-image cover-{{ $booking->rooms->first()->room->id }} cover-active">
            <div class="mt-3 position-relative">
                <div class="position-absolute bottom-0 left-0 w-100 cover-title p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ $booking->rooms->first()->room->name }}</span>
                    </div>
                </div>
                <section class="splide" aria-label="{{ $room['name'] }} gallery">
                  <div class="splide__track">
                        <ul class="splide__list">
                            @if (@file_exists(public_path('tenancy/assets/images/rooms/'. $room['room_id'] .'/'. $room['featured_image'])))
                                <li class="splide__slide"><img src="{{ asset('images/rooms/'. $room['room_id'] .'/'. $room['featured_image']) }}" alt="" /></li>
                            @endif
                            @foreach ($room['gallery'] as $path)
                                <li class="splide__slide"><img src="{{ asset('images/rooms/'. $room['room_id'] .'/'. $path) }}" alt="" /></li>
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
              <span class="room-accommodation">
                @if ($booking->rooms->first()->room->room_type == 'Private')
                      {{ $booking->rooms->first()->room->room_type }}
                  @else
                      @if ($guest <= 1)
                          {{ session('room')['private_booking'] ? 'PRIVATE' : 'SHARED WITH OTHER GUESTS' }}
                      @else
                          {{ $booking->rooms->first()->room->room_type }}
                      @endif
                  @endif
              </span>
                        </b>
                    </p>
                    <div>
                        @if ($room['availability_status'] == 1)
                            <b class="text-success">ROOM IS AVAILABLE</b>
                        @elseif ($room['availability_status'] == 2)
                            <b class="text-orange">LIMITED AVAILABILITY</b>
                        @elseif ($room['availability_status'] == 3)
                            <b class="text-danger">FULL</b>
                        @endif
                    </div>
                </div>
                <div class="border-top-1 border-alpha-grey py-3 my-2 border-bottom-1">
                    {!! nl2br($booking->rooms->first()->room->room_short_description) !!}
                </div>
            </div>
        </div>

        @if (count($addons) > 0)
            <h4><b>Add-ons</b></h4>
            @foreach ($addons as $addon)
                <div class="d-flex justify-content-start align-items-start py-3">
                    <div class="mr-5">
                        <img
                            src="{{ asset('images/addons/'. tenant('id') .'_addon_'. $addon['id'] .'.jpg?'. date('Ymd')) }}"
                            class="rounded d-block" style="width: 100px"/>
                    </div>
                    <div class="w-50 mr-2">
                        <div class="text-uppercase font-size-lg"><b>{{ $addon['name'] }}</b></div>
                        <div class="mt-2">
                            {{ $addon['guests'] .' '. Str::plural($addon['unit_name'], $addon['guests']) }}{{ $addon['amount'] > 1 ? ', '. $addon['amount'] .' '. Str::plural('day', $addon['amount']) : '' }}
                        </div>
                    </div>
                    <div class="form-check ml-auto align-self-center">
                        {!! $addon['total'] > 0 ? '&euro;'. number_format($addon['total']) : 'FREE' !!}
                    </div>
                </div>

                @if (
                    isset($addon['questionnaire'])
                    && $addon['questionnaire']
                    && isset($addon['questionnaire']['answers'])
                    && count($addon['questionnaire']['answers']) > 0
                    && isset($addon['questionnaire']['name'])
                )
                    <div class="d-flex justify-content-start align-items-start py-3">
                        <div class="w-50 mr-2">
                            <div class="text-uppercase font-size-lg"><b>{{ $addon['questionnaire']['name'] }}</b></div>
                            @foreach($addon['questionnaire']['answers'] as $answer)
                                <div class="mt-2">
                                    {{ implode(' ,', $answer) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @endif

        @if (count($transfers) > 0)
            <div class="border-top-1 pt-3 mt-3 border-alpha-grey">
                <h4><b>Transfers</b></h4>
                @foreach ($transfers as $transfer)
                    <div class="d-flex justify-content-start align-items-start py-3">
                        <div class="mr-5">
                            <img
                                src="{{ asset('images/transfers/'. tenant('id') .'_transfer_'. $transfer['id'] .'.jpg?'. date('Ymd')) }}"
                                class="rounded d-block" style="width: 100px"/>
                        </div>
                        <div class="w-50 mr-2">
                            <div class="text-uppercase font-size-lg"><b>{{ $transfer['name'] }}</b></div>
                            <div class="mt-2">
                                {{ $transfer['guests'] .' '. Str::plural('guest', $transfer['guests']) }}
                            </div>
                        </div>
                        <div class="form-check ml-auto align-self-center">
                            {!! $transfer['total'] > 0 ? '&euro;'. number_format($transfer['total']) : 'FREE' !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="border-top-1 pt-3 mt-3 border-alpha-grey">
            <h4><b>Guest Details</b></h4>

            <p>
                <b>{{ $booking->guest->details->full_name }}</b>
                <br/>
                <b><span class="link-custom">{{ $booking->guest->details->email }}</span></b>
                <br/>
                {{ $booking->guest->details->street }}
                <br/>
                {{ $booking->guest->details->city }}, {{ $booking->guest->details->country }}
                , {{ $booking->guest->details->zip }}
                <br/>
                {{ $booking->guest->details->phone != '' ? 'Phone: '. $booking->guest->details->phone : '' }}
            </p>
        </div>

        <form action="/book-now/confirmed?state={{ $state . $ga }}" method="post">
            <div class="d-flex justify-content-between mt-5">
                @csrf
                <a class="btn bg-grey text-uppercase font-size-lg btn-lg"
                   href="/book-now/details?=ret{{ $ga }}">BACK</a>
                <button class="btn btn-custom text-uppercase font-size-lg btn-lg" type="submit">CONFIRM AND PAY</button>
            </div>
        </form>

    </div>
@endif
