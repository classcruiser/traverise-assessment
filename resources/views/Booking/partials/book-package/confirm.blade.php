<div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">3 - Confirm Bookings</div>
@if($step == 3)
    <div class="section-body {{$step == 3 ? 'active' : ''}}">
        <h4><b>Room</b></h4>
        <div class="cover-image cover-active">
            <div class="mt-3 position-relative">
                <div class="position-absolute bottom-0 left-0 w-100 cover-title p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{$package->room->name}}</span>
                    </div>
                </div>
                <img class="img-fluid rounded" src="{{ asset('images/rooms/'. tenant('id') .'_room_'. $booking->rooms->first()->room->id .'.jpg?'. date('Ymd')) }}" alt="{{$package->room->name}}" />
            </div>
            <div class="py-3">
                <div class="d-flex justify-content-between align-items-start">
                    <p class="text-uppercase">Accommodation: <b>{{$package->room->beds[0]}}</b></p>
                </div>
                <div class="border-top-1 border-alpha-grey py-3 my-2 border-bottom-1">
                    {!! nl2br($package->room->room_short_description) !!}
                </div>
            </div>
        </div>

        @if(count($addons) > 0)
            <h4><b>Add-ons</b></h4>
            @foreach($addons as $addon)
                <div class="d-flex justify-content-start align-items-start py-3">
                    <div class="mr-5">
                        <img src="{{ asset('images/addons/'. tenant('id') .'_addon_'. addon['id'] .'.jpg?'. date('Ymd')) }}" class="rounded d-block" style="width: 100px"/>
                    </div>
                    <div class="w-50 mr-2">
                        <div class="text-uppercase font-size-lg"><b>{{$addon->details->name}}</b></div>
                        <div class="mt-2">
                            {{$guest .' '. Str::plural('guest', $guest)}}{{$addon->qty > 1 ? ', '. $addon->qty .' '. Str::plural('day', $addon->qty) : ''}}
                        </div>
                    </div>
                    <div class="form-check ml-auto align-self-center">
                        FREE
                    </div>
                </div>
            @endforeach
        @endif

        @if(count($extras) > 0)
            <h4><b>Add-ons</b></h4>
            @foreach($extras as $extra)
                <div class="d-flex justify-content-start align-items-start py-3">
                    <div class="mr-5">
                        <img src="{{ asset('images/addons/'. tenant('id') .'_addon_'. $extra['id'] .'.jpg?'. date('Ymd')) }}" class="rounded d-block" style="width: 100px"/>
                    </div>
                    <div class="w-50 mr-2">
                        <div class="text-uppercase font-size-lg"><b>{{$extra['name']}}</b></div>
                        <div class="mt-2">
                            {{$extra['guests'] .' '. Str::plural('guest', $extra['guests'])}}{{$extra['amount'] > 1 ? ', '. $extra['amount'] .' '. Str::plural('day', $extra['amount']) : ''}}
                        </div>
                    </div>
                    <div class="form-check ml-auto align-self-center">
                        &euro;{{number_format($extra['price'], 2)}}
                    </div>
                </div>
            @endforeach
        @endif

        @if($package->airport_pickup || $package->airport_dropoff)
            <div class="border-top-1 pt-3 mt-3 border-alpha-grey">
                <h4><b>Transfers</b></h4>
                @if($package->airport_pickup)
                    <div class="d-flex justify-content-start align-items-start py-3">
                        <div class="mr-5">
                            <img src="{{ asset('images/transfers/'. tenant('id') .'_transfer_'. $pickup->id .'.jpg?'. date('Ymd')) }}" class="rounded d-block" style="width: 100px"/>
                        </div>
                        <div class="w-50 mr-2">
                            <div class="text-uppercase font-size-lg"><b>{{$pickup->name}}</b></div>
                            <div class="mt-2">
                                {{$guest .' '. Str::plural('guest', $guest)}}
                            </div>
                        </div>
                        <div class="form-check ml-auto align-self-center">
                            FREE
                        </div>
                    </div>
                @endif
                @if($package->airport_dropoff)
                    <div class="d-flex justify-content-start align-items-start py-3">
                        <div class="mr-5">
                            <img src="{{ asset('images/transfers/'. tenant('id') .'_transfer_'. $dropoff->id .'.jpg?'. date('Ymd')) }}" class="rounded d-block" style="width: 100px"/>
                        </div>
                        <div class="w-50 mr-2">
                            <div class="text-uppercase font-size-lg"><b>{{$dropoff->name}}</b></div>
                            <div class="mt-2">
                                {{$guest .' '. Str::plural('guest', $guest)}}
                            </div>
                        </div>
                        <div class="form-check ml-auto align-self-center">
                            FREE
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="border-top-1 pt-3 mt-3 border-alpha-grey">
            <h4><b>Guest Details</b></h4>

            <p>
                <b>{{$booking->guest->details->full_name}}</b>
                <br />
                {{$booking->guest->details->email}}
                <br />
                {{$booking->guest->details->street}}
                <br />
                {{$booking->guest->details->city}}, {{$booking->guest->details->country}}, {{$booking->guest->details->zip}}
                <br />
                Phone: {{$booking->guest->details->phone}}
            </p>
        </div>

        <form action="/book-package/{{$package->slug}}/confirm" method="post">
            <div class="d-flex justify-content-between mt-5">
                @csrf
                <a class="btn bg-grey text-uppercase font-size-lg btn-lg" href="/book-package/{{$package->slug}}">BACK</a>
                <button class="btn btn-custom text-uppercase font-size-lg btn-lg" type="submit">CONFIRM BOOKING</button>
            </div>
        </form>

    </div>
@endif
