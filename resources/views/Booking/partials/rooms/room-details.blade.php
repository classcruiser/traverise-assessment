<form action="{{ route('tenant.rooms.updateRoomDetails', [ 'id' => $room->id ]) }}" method="post" id="room-details">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-4"><h6>Details</h6></div>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>* Location</label>
                            <select class="form-control select-no-search" data-fouc data-placeholder="Location" name="location_id" required>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" {{ $location->id == $room->location_id ? 'selected' : '' }}>{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>* Name</label>
                            <input type="text" name="name" placeholder="Name" class="form-control" value="{{ $room->name }}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>* Room type</label>
                            <select class="form-control select-no-search" data-fouc data-placeholder="Room type" name="room_type" required>
                                <option value="Private" {{ $room->room_type == 'Private' ? 'selected' : '' }}>Private</option>
                                <option value="Shared" {{ $room->room_type == 'Shared' ? 'selected' : '' }}>Shared</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>* Availability</label>
                            <select class="form-control select-no-search" data-fouc data-placeholder="Room type" name="availability" required>
                                <option value="auto" {{ $room->availability == 'auto' ? 'selected' : '' }}>Automatic</option>
                                <option value="pending" {{ $room->availability == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $room->availability == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>* Private room space</label>
                            <input type="number" name="private_space" placeholder="Private room space" class="form-control" value="{{ $room->private_space }}" />
                            <div class="font-size-sm text-muted mt-2">
                                Only if room type is Private. Enter the amount of space it will take if guest book a private room. Enter 0 to book all the space in the room
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="active" class="custom-control-input" id="form-active" {{ $room->active ? 'checked' : '' }}>
                                <label class="custom-control-label" for="form-active">Active</label>
                            </div>
                            <div class="font-size-sm text-muted">
                                Inactive rooms are not displayed in booking process
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="admin_active" class="custom-control-input" id="form-admin_active" {{ $room->admin_active ? 'checked' : '' }}>
                                <label class="custom-control-label" for="form-admin_active">Admin Active</label>
                            </div>
                            <div class="font-size-sm text-muted">
                                Inactive rooms are not displayed in backend booking process
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="allow_pending" class="custom-control-input" id="form-pending" {{ $room->allow_pending ? 'checked' : '' }}>
                                <label class="custom-control-label" for="form-pending">Allow pending bookings</label>
                            </div>
                            <div class="font-size-sm text-muted">
                                When disabled, pending booking will be automatically rejected.
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="calendar_visibility" class="custom-control-input" id="form-calendar" {{ $room->calendar_visibility ? 'checked' : '' }}>
                                <label class="custom-control-label" for="form-calendar">Show in Calendar</label>
                            </div>
                            <div class="font-size-sm text-muted">
                                Set whether to show this room category on the calendar
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-4"><h6>Properties</h6></div>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="d-block mb-2">Bed types</label>
                            <div class="custom-control custom-checkbox custom-control-inline mt-1">
                                <input type="checkbox" name="bed_type[]" value="Single" class="custom-control-input" id="form-single" {{ $room->bed_types()->contains('Single') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="form-single">Single</label>
                            </div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" name="bed_type[]" value="Double" class="custom-control-input" id="form-double" {{ $room->bed_types()->contains('Double') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="form-double">Double</label>
                            </div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" name="bed_type[]" value="Twin" class="custom-control-input" id="form-twin" {{ $room->bed_types()->contains('Twin') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="form-twin">Twin</label>
                            </div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" name="bed_type[]" value="King Size" class="custom-control-input" id="form-king" {{ $room->bed_types()->contains('King Size') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="form-king">King Size</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Bathroom</label>
                            <select class="form-control select-no-search" data-fouc data-placeholder="Bathroom type" name="bathroom_type" required>
                                <option value="Private" {{ $room->bathroom_type == 'Private' ? 'selected' : '' }}>Private</option>
                                <option value="Shared" {{ $room->bathroom_type == 'Shared' ? 'selected' : '' }}>Shared</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Smoking</label>
                            <select class="form-control select-no-search" data-fouc data-placeholder="Bathroom type" name="smoking" required>
                                <option value="0" {{ $room->smoking == 0 ? 'selected' : '' }}>Not allowed</option>
                                <option value="1" {{ $room->smoking == 1 ? 'selected' : '' }}>Allowed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Min. Guest</label>
                            <input type="number" min="1" max="10" name="min_guest" placeholder="Minimum guest to book this room (1 to 10)" class="form-control" value="{{ $room->min_guest }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Max. Guest</label>
                            <input type="number" min="1" max="10" name="max_guest" placeholder="Maximum guest to book this room (1 to 10)" class="form-control" value="{{ $room->max_guest }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Min. Nights</label>
                            <input type="number" min="1" name="min_nights" placeholder="Minimum booking nights" class="form-control" value="{{ $room->min_nights }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Max. Nights</label>
                            <input type="number" min="1" max="10" name="max_nights" placeholder="Leave empty for no limit" class="form-control" value="{{ $room->max_nights }}">
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-sm-4"><h6>Last Room Threshold</h6></div>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Threshold</label>
                            <div class="input-group">
                                <input type="number" min="0" max="100" name="limited_threshold" placeholder="Enter number" class="form-control" value="{{ $room->limited_threshold }}">
                                <span class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </span>
                            </div>
                            <div class="font-size-sm text-muted mt-2">
                                Show availability as "Last Room" when room occupancy falls below certain percentage. Enter 0 to disable
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-sm-4"><h6>Other</h6></div>
            <div class="col-sm-8">
                <div class="row">

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Short Description</label>
                            <textarea name="room_short_description" class="form-control" rows="6" placeholder="Room description">{!! $room->room_short_description !!}</textarea>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Full Description</label>
                            <textarea name="room_description" class="form-control" rows="12" placeholder="Room description">{!! $room->room_description !!}</textarea>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Room Inclusions</label>
                            <textarea name="inclusions" class="form-control" rows="20" placeholder="Room inclusions">{!! $room->inclusions !!}</textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
    </div>

    @can ('edit room')
        <div class="card-body">
            <div class="text-right">
                @csrf
                <input type="hidden" name="room_id" id="room_id" value="{{ $room->id }}" />
                <button class="btn bg-danger update-room-details">Submit</button>
            </div>
        </div>
    @endcan
    <!-- end card body -->

</form>
