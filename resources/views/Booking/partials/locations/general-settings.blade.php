<form action="javascript:" method="post" id="camp-details">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-4">
                <h6>Camp Details</h6>
            </div>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>* Name</label>
                            <input type="text" name="name" placeholder="Name" class="form-control" value="{{$location->name}}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Short name</label>
                            <input type="text" name="short_name" placeholder="Short name" class="form-control" value="{{$location->short_name}}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Abbreviation</label>
                            <input type="text" name="abbr" placeholder="Abbreviation" class="form-control" value="{{$location->abbr}}">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Location address</label>
                            <textarea name="address" placeholder="Address" class="form-control" rows="8">{{$location->address}}</textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Shop Title</label>
                            <input type="text" name="title" placeholder="Title that will appear in the Shop page" class="form-control" value="{{ $location->title }}">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Shop Sub Title</label>
                            <textarea name="subtitle" placeholder="Subtitle that will appear in the Shop page" class="form-control frl-short" rows="3">{{$location->subtitle}}</textarea>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="text" name="contact_email" placeholder="Email" class="form-control" value="{{$location->contact_email}}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Phone number</label>
                            <input type="text" name="phone" placeholder="Phone" class="form-control" value="{{$location->phone}}">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="active" class="custom-control-input" {{$location->active ? 'checked' : ''}} id="form-active">
                                <label class="custom-control-label" for="form-active">Active ?</label>
                            </div>
                            <div class="font-size-sm text-muted">
                                Inactive camp is not displayed in booking process
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-sm-4">
                <h6>Description</h6>
            </div>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <textarea name="description" class="frl form-control">{!! $location->description !!}</textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="card-body {{ tenant('plan') == 'events' ? 'd-none' : '' }}">
        <div class="row">
            <div class="col-sm-4">
                <h6>Advanced Settings</h6>
            </div>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Private Room</label>
                            <select class="form-control select-no-search" data-fouc data-placeholder="Private room pricing" name="price_type">
                                <option value="room" {{$location->price_type == 'room' ? 'selected' : ''}}>Priced by Room</option>
                                <option value="guest" {{$location->price_type == 'guest' ? 'selected' : ''}}>Priced by Guest</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Minimum Booking Nights</label>
                            <input type="number" name="minimum_nights" placeholder="1" class="form-control" value="{{$location->minimum_nights}}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Maximum Booking Nights</label>
                            <input type="number" name="maximum_nights" placeholder="Leave empty for no limit" class="form-control" value="{{$location->maximum_nights}}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Minimum Check in date</label>
                            <input type="text" name="minimum_checkin" class="form-control date-range-single" value="{{$location->minimum_checkin}}">
                        </div>
                    </div>
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="d-block mb-2">Allow pending booking?</label>
                            <div class="custom-control custom-checkbox custom-control-inline mt-1">
                                <input type="checkbox" name="allow_pending" class="custom-control-input" id="form-allow-pending" {{$location->allow_pending ? 'checked' : ''}}>
                                <label class="custom-control-label" for="form-allow-pending">Allow pending booking</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body {{ tenant('plan') == 'events' ? 'd-none' : '' }}">
        <div class="row">
            <div class="col-sm-4">
                <h6>Arrival Rule</h6>
                <p class="text-grey-400">Add condition for arrival</p>
            </div>
            <div class="col-sm-8">
                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox custom-control-inline mt-1">
                                <input type="checkbox" name="has_arrival_rule" class="custom-control-input" id="form-rule" {{$location->has_arrival_rule ? 'checked' : ''}} data-target="#rule-form" data-toggle="collapse">
                                <label class="custom-control-label" for="form-rule">Enable?</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6"></div>
                    <div class="col-sm-12 collapse {{$location->has_arrival_rule ? 'show' : ''}}" id="rule-form">
                        <div class="row">

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>On dates</label>
                                    <div id="type-dates">
                                        <input type="text" name="rule_period" class="form-control date-range-simple" value="{{$location->rule ? $location->rule->period : ''}}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Disable Check in on</label>
                                    <div>
                                        <select class="form-control multiselect" name="disable_check_in_days[]" multiple="multiple" data-fouc>
                                            @foreach($days as $day)
                                                <option value="{{$day}}" {{$location->rule && in_array($day, $location->rule->disable_check_ins) ? 'selected' : ''}}>{{$day}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Disable Check out on</label>
                                    <div>
                                        <select class="form-control multiselect" name="disable_check_out_days[]" multiple="multiple" data-fouc>
                                            @foreach($days as $day)
                                                <option value="{{$day}}" {{$location->rule && in_array($day, $location->rule->disable_check_outs) ? 'selected' : ''}}>{{$day}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="card-body {{ tenant('plan') == 'events' ? 'd-none' : '' }}">
        <div class="row">
            <div class="col-sm-4">
                <h6>Deposit Settings</h6>
            </div>
            <div class="col-sm-8">
                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox custom-control-inline mt-1">
                                <input type="checkbox" name="enable_deposit" class="custom-control-input" id="form-deposit" {{$location->enable_deposit ? 'checked' : ''}} data-target="#deposit-form" data-toggle="collapse">
                                <label class="custom-control-label" for="form-deposit">Enable?</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6"></div>
                    <div class="col-sm-12 collapse {{$location->enable_deposit ? 'show' : ''}}" id="deposit-form">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Deposit type</label>
                                    <select class="form-control select-no-search" data-fouc data-placeholder="Deposit type" name="deposit_type">
                                        <option value="fixed" {{$location->deposit_type == 'fixed' ? 'selected' : ''}}>Fixed </option>
                                        <option value="percent" {{$location->deposit_type == 'percent' ? 'selected' : ''}}>Percent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Value</label>
                                    <input type="number" name="deposit_value" placeholder="0.0" class="form-control" value="{{$location->deposit_value}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Due in</label>
                                    <div class="input-group">
                                        <input type="number" name="deposit_due" class="form-control" value="{{$location->deposit_due}}" />
                                        <span class="input-group-append">
                                            <span class="input-group-text">Days</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="card-body {{ tenant('plan') == 'events' ? 'd-none' : '' }}">
        <div class="row">
            <div class="col-sm-4">
                <h6>Duration Discount</h6>
            </div>
            <div class="col-sm-8">
                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox custom-control-inline mt-1">
                                <input type="checkbox" name="duration_discount" class="custom-control-input" id="form-duration" {{$location->duration_discount ? 'checked' : ''}} data-target="#duration-form" data-toggle="collapse">
                                <label class="custom-control-label" for="form-duration">Enable?</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6"></div>
                    <div class="col-sm-12 collapse {{$location->duration_discount ? 'show' : ''}}" id="duration-form">
                        <div class="row">

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Daily discount</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-percent"></i></span>
                                        </span>
                                        <input type="number" name="min_discount" class="form-control form-control-sm" value="{{$location->min_discount}}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Max discount</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-percent"></i></span>
                                        </span>
                                        <input type="number" name="max_discount" class="form-control form-control-sm" value="{{$location->max_discount}}" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    @can('edit camp')
        <div class="card-body">
            <div class="text-right">
                @csrf
                <input type="hidden" name="location_id" id="location_id" value="{{$location->id}}" />
                <button class="btn bg-slate update-camp-details text-uppercase" data-url="{{route('tenant.camps.details', $location->id)}}">Submit</button>
            </div>
        </div>
    @endcan
    <!-- end card body -->

</form>