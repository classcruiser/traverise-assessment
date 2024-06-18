<div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">1 - Add-ons</div>
@if($step == 1)
    <div class="section-body {{$step == 1 ? 'active' : ''}}">

        <form action="/book-package/{{$slug}}" method="post">
            <input type="hidden" class="addon-origin" value="book-package" />
            <input type="hidden" class="sp-duration" value="{{session('sp_duration')}}" />
            <input type="hidden" class="sp-slug" value="{{session('sp_slug')}}" />
            <div class="cover-image cover-{{$package->room->id}} cover-active">

                <div class="pt-1 pb-2">
                    <h4><b>Available Add-ons</b></h4>

                    @foreach($addons as $addon)
                        <div class="d-flex justify-content-start align-items-start py-3">
                            <div class="mr-4 mobile-hide">
                                <img src="{{ asset('images/addons/'. tenant('id') .'_addon_'. $addon->id .'.jpg?'. date('Ymd')) }}" class="rounded d-block" style="width: 100px"/>
                            </div>
                            <div class="w-75 mr-4">
                                <div class="text-uppercase font-size-lg">
                                    @if($addon->page_link != '' && $addon->page_link)
                                        <a href="{{$addon->page_link}}" title="" class="text-kima" target="_blank"><b>{{$addon->name}}</b> <span class="ml-1" style="color: #999; font-size: .8em; display: inline-block; border-bottom: 1px dashed #ddd;">DETAILS <i class="far fa-external-link ml-1"></i></span></a>
                                    @else
                                        <b>{{$addon->name}}</b>
                                    @endif
                                </div>
                                <div class="text-muted mb-2">
                                    @if($addon->base_price <= 0)
                                        <span>FREE</span>
                                    @else
                                        <span class="price-{{$addon->id}}">
                                            @if($sp_extras->contains('id', $addon->id))
                                                &euro;{{collect($sp_extras)->where('id', $addon->id)->first()['total']}}
                                            @else
                                                &euro;{{$addon->total}}</b>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <select
                                        name="addon[{{ $addon->id }}][guest]"
                                        class="form-control mr-1 addon-guest addon-guest-{{ $addon->id }} addon-select-{{ $addon->id }}" {{ $sp_extras->contains('id', $addon->id) ? 'disabled' : '' }}
                                        data-id="{{ $addon->id }}"
                                    >
                                        @for ($i = 1; $i <= session('sp_guest'); $i++)
                                            <option
                                                value="{{ $i }}"
                                                @if ($sp_extras->contains('id', $addon->id))
                                                    {{ collect($sp_extras)->where('id', $addon->id)->first()['guests'] == $i ? 'selected' : '' }}
                                                @else
                                                    {{ 1 == $i ? 'selected' : '' }}
                                                @endif
                                            >
                                                {{$i}} {{\Illuminate\Support\Str::plural('guest', $i)}}
                                            </option>
                                        @endfor
                                    </select>
                                    @if($addon->rate_type == 'Day')
                                        <select name="addon[{{$addon->id}}][duration]" class="form-control ml-1 addon-duration addon-duration-{{$addon->id}} addon-select-{{$addon->id}}" {{$sp_extras->contains('id', $addon->id) ? 'disabled' : ''}} data-id="{{$addon->id}}">
                                            @for ($i = 1; $i <= $duration; $i++)
                                                <option
                                                    value="{{ $i }}"
                                                    @if ($sp_extras->contains('id', $addon->id))
                                                        {{ collect($sp_extras)->where('id', $addon->id)->first()['amount'] == $i ? 'selected' : '' }}
                                                    @else
                                                        {{ $duration == $i ? 'selected' : '' }}
                                                    @endif
                                                >
                                                    {{ $i }} {{ \Illuminate\Support\Str::plural('day', $i) }}
                                                </option>
                                            @endfor
                                        </select>
                                    @endif
                                </div>

                                @if(!$addon->page_link || $addon->page_link == '')
                                    <div class="mt-2">
                                        <p class="mb-0">{{$addon->description}}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="form-check ml-auto {{(!$addon->page_link || $addon->page_link == '') ? 'align-self-center' : 'align-self-end'}} pb-2 h-100">
                                <label class="form-check-label">
                                    <input type="checkbox" id="addon_{{$addon->id}}" name="addon[{{$addon->id}}]" class="form-check-input-styled mr-1 addon-check" data-fouc {{$sp_extras->contains('id', $addon->id) ? 'checked' : ''}} data-id="{{$addon->id}}" />
                                </label>
                            </div>
                        </div>
                    @endforeach

                    @foreach($transfers as $transfer)
                        <div class="d-flex justify-content-start align-items-start py-3">
                            <div class="mr-4 mobile-hide">
                                <img src="{{ asset('images/transfers/'. tenant('id') .'_transfer_'. $transfer->id .'.jpg?'. date('Ymd')) }}" class="rounded d-block" style="width: 100px"/>
                            </div>
                            <div class="w-50 mr-2">
                                <div class="text-uppercase font-size-lg"><b>{{$transfer->name}}</b></div>
                                <div class="text-muted">
                                    {!! $transfer->price > 0 ? '<span class="transfer-price-'. $transfer->id .'">&euro;'. $transfer->price .'</span>' : '<span class="text-success"><b>FREE</b></span>' !!}
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <select
                                        name="transfer[{{ $transfer->id }}][guest]"
                                        class="form-control mr-1 transfer-guest transfer-guest-{{ $transfer->id }} transfer-select-{{ $transfer->id }}" {{ $sp_transfers->contains('id', $transfer->id) ? 'disabled' : '' }}
                                        data-id="{{ $transfer->id }}"
                                    >
                                        @for ($i = 1; $i <= session('sp_guest'); $i++)
                                            <option
                                                value="{{ $i }}"
                                                @if ($sp_transfers->contains('id', $transfer->id))
                                                    {{ collect($sp_transfers)->where('id', $transfer->id)->first()['guests'] == $i ? 'selected' : '' }}
                                                @else
                                                    {{ session('sp_guest') == $i ? 'selected' : '' }}
                                                @endif
                                            >
                                                {{$i}} {{\Illuminate\Support\Str::plural('guest', $i)}}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="form-check ml-auto align-self-center">
                                <label class="form-check-label">
                                    <input type="checkbox" id="transfer_{{$transfer->id}}" name="transfer[{{$transfer->id}}]" class="form-check-input-styled mr-1 transfer-check" data-fouc {{$sp_transfers->contains('id', $transfer->id) ? 'checked' : ''}} data-id="{{$transfer->id}}" />
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a class="btn btn-custom text-uppercase font-size-lg btn-lg" href="/book-package/{{$slug}}/details">NEXT STEP</a>
            </div>
        </form>
    </div>
@endif
