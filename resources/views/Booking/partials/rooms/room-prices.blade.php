<form action="javascript:" method="post" id="room-prices">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-4"><h6>Base Pricing</h6></div>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>* Default price</label>
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <span class="input-group-text">&euro;</span>
                                </span>
                                <input type="text" name="default_price" class="form-control" value="{{ $room->default_price }}" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label>&nbsp;</label>
                        <div class="py-2 text-muted">
                            <i class="fa fa-info fa-fw"></i> Shared rooms are always priced by guest.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-4"><h6>Guest Pricing</h6></div>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Surcharge (LOW)</label>
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <span class="input-group-text">&euro;</span>
                                </span>
                                <input type="text" name="empty_fee_low" class="form-control" value="{{ $room->empty_fee_low }}" />
                            </div>
                            <div class="text-muted font-size-sm mt-1">applied if room gets booked as private</div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Surcharge (MAIN)</label>
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <span class="input-group-text">&euro;</span>
                                </span>
                                <input type="text" name="empty_fee_main" class="form-control" value="{{ $room->empty_fee_main }}" />
                            </div>
                            <div class="text-muted font-size-sm mt-1">applied if room gets booked as private</div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Surcharge (PEAK)</label>
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <span class="input-group-text">&euro;</span>
                                </span>
                                <input type="text" name="empty_fee_peak" class="form-control" value="{{ $room->empty_fee_peak }}" />
                            </div>
                            <div class="text-muted font-size-sm mt-1">applied if room gets booked as private</div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Surcharge (SPECIAL)</label>
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <span class="input-group-text">&euro;</span>
                                </span>
                                <input type="text" name="empty_fee_special" class="form-control" value="{{ $room->empty_fee_special }}" />
                            </div>
                            <div class="text-muted font-size-sm mt-1">applied if room gets booked as private</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="d-block mb-2">Private booking</label>
                            <div class="custom-control custom-checkbox custom-control-inline mt-1">
                                <input type="checkbox" name="allow_private" class="custom-control-input" id="form-private" {{ $room->allow_private ? 'checked' : '' }}>
                                <label class="custom-control-label" for="form-private">Allow private booking?</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-sm-4"><h6>Progressive Pricing</h6></div>
            <div class="col-sm-8">
                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox custom-control-inline mt-1">
                                <input
                                type="checkbox"
                                name="progressive_pricing"
                                class="custom-control-input"
                                id="form-progressive" {{ $room->progressive_pricing ? 'checked' : '' }}
                                data-target="#progressive-pricing"
                                data-toggle="collapse"
                                >
                                <label class="custom-control-label" for="form-progressive">Enable?</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="#" class="alpha-success text-success-800 btn add-threshold" data-capacity="{{ $room->total_capacity }}">
                            <i class="fal fa-plus"></i> Add Threshold
                        </a>
                    </div>
                    <div class="col-sm-12 collapse {{ $room->progressive_pricing ? 'show' : '' }}" id="progressive-pricing">

                        @if ($room->progressive_prices->count() > 0)
                            @foreach ($room->progressive_prices->sortByDesc('beds')->all() as $pr)
                                <div class="py-2 border-bottom-1 border-alpha-grey" id="key-{{ $pr->id }}">
                                    <div class="d-flex justify-content-start align-items-center">

                                        <div style="width: 120px;" class="mr-2">
                                            <select class="form-control select-no-search" data-fouc data-placeholder="Bed number" name="progressive_price[{{ $pr->id }}][beds]">
                                                @for ($i = 0; $i <= $room->total_capacity; $i++)
                                                <option value="{{ $i }}" {{ $pr->beds == $i ? 'selected' : '' }}>{{ $i }} {{ Str::plural('bed', $i) }}</option>
                                                @endfor
                                            </select>
                                        </div>

                                        <div>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fal fa-plus fa-fw"></i></span>
                                                    <span class="input-group-text"><i class="fal fa-percent fa-fw"></i></span>
                                                </span>
                                                <input type="text" class="form-control" placeholder="Percentage" name="progressive_price[{{ $pr->id }}][amount]" value="{{ number_format($pr->amount, 1) }}" style="width: 70px"/>
                                            </div>
                                        </div>

                                        <a
                                            href="#"
                                            title=""
                                            class="ml-auto btn alpha-danger text-danger-800 btn-icon btn-sm delete-pp"
                                            data-target="key-{{ $pr->id }}"
                                            data-source="database"
                                            data-room-id="{{ $room->id }}"
                                            data-id="{{ $pr->id }}"
                                            >
                                            <i class="fal fa-fw fa-times"></i>
                                        </a>

                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-sm-4"><h6>Occupancy Surcharge</h6></div>
            <div class="col-sm-8">
                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox custom-control-inline mt-1">
                                <input
                                type="checkbox"
                                name="occupancy_surcharge"
                                class="custom-control-input"
                                id="form-occupancy_surcharge" {{ $room->occupancy_surcharge ? 'checked' : '' }}
                                data-target="#occupancy_surcharge"
                                data-toggle="collapse"
                                >
                                <label class="custom-control-label" for="form-occupancy_surcharge">Enable?</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="#" class="alpha-success text-success-800 btn add-threshold-surcharge" data-capacity="{{ $room->total_capacity }}">
                            <i class="fal fa-plus"></i> Add Threshold
                        </a>
                    </div>
                    <div class="col-sm-12 collapse {{ $room->occupancy_surcharge ? 'show' : '' }}" id="occupancy_surcharge">

                        @if ($room->occupancy_prices()->exists())
                            @foreach ($room->occupancy_prices->sortBy('pax')->all() as $pr)
                                <div class="py-2 border-bottom-1 border-alpha-grey" id="key-{{ $pr->id }}">
                                    <div class="d-flex justify-content-start align-items-center">

                                        <div class="d-flex justify-content-start align-items-center" style="width: 600px;">
                                            <div class="input-group mr-2">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">Pax</span>
                                                </span>
                                                <input type="text" class="form-control new-occupancy_surcharge-field" placeholder="0.0" name="occupancy_price[{{ $pr->id }}][pax]" value="{{ $pr->pax }}" style="width: 50px;"/>
                                            </div>
                                            <div class="input-group mr-2">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">Low &euro;</span>
                                                </span>
                                                <input type="text" class="form-control new-occupancy_surcharge-field" placeholder="0.0" name="occupancy_price[{{ $pr->id }}][amount_low]" style="width: 50px" value="{{ $pr->amount_low }}" />
                                            </div>
                                            <div class="input-group mr-2">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">Main &euro;</span>
                                                </span>
                                                <input type="text" class="occupancy-fields form-control new-occupancy_surcharge-field" placeholder="0.0" name="occupancy_price[{{ $pr->id }}][amount_main]" style="width: 50px" value="{{ $pr->amount_main }}" />
                                            </div>
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">Peak &euro;</span>
                                                </span>
                                                <input type="text" class="form-control new-occupancy_surcharge-field" placeholder="0.0" name="occupancy_price[{{ $pr->id }}][amount_peak]" style="width: 50px" value="{{ $pr->amount_peak }}" />
                                            </div>
                                        </div>

                                        <a
                                            href="#"
                                            title=""
                                            class="ml-auto btn alpha-danger text-danger-800 btn-icon btn-sm delete-op"
                                            data-target="key-{{ $pr->id }}"
                                            data-source="database"
                                            data-room-id="{{ $room->id }}"
                                            data-id="{{ $pr->id }}"
                                            >
                                            <i class="fal fa-fw fa-times"></i>
                                        </a>

                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </div>

    @can('edit room')
        <div class="card-body">
            <div class="text-right">
                @csrf
                <input type="hidden" name="room_id" id="room_id" value="{{ $room->id }}" />
                <button class="btn bg-danger update-room-prices">Submit</button>
            </div>
        </div>
    @endcan
    <!-- end card body -->

</form>
