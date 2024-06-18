@extends('Booking.app')

@section('content')

<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item"><a href="{{ route('tenant.rooms') }}" title="" class="text-grey">Rooms</a></span>
            <span class="breadcrumb-item active">New Room Category</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h4 class="card-title">New Room Category</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.rooms') }}" title="" class="btn btn-link text-slate">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <div class="tab-content">
                        <div class="tab-pane active">
                            
                            <form action="{{ route('tenant.rooms.insert') }}" method="post" id="room-details">
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
                                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Name</label>
                                                        <input type="text" name="name" placeholder="Name" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Room type</label>
                                                        <select class="form-control select-no-search" data-fouc data-placeholder="Room type" name="room_type" required>
                                                            <option value="Private">Private</option>
                                                            <option value="Shared">Shared</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Availability</label>
                                                        <select class="form-control select-no-search" data-fouc data-placeholder="Room type" name="availability" required>
                                                            <option value="auto" selected>Automatic</option>
                                                            <option value="pending">Pending</option>
                                                            <option value="confirmed">Confirmed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="active" class="custom-control-input" id="form-active">
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
                                                            <input type="checkbox" name="admin_active" class="custom-control-input" id="form-admin_active">
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
                                                            <input type="checkbox" name="allow_pending" class="custom-control-input" id="form-pending">
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
                                                            <input type="checkbox" name="calendar_visibility" class="custom-control-input" id="form-calendar">
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
                                                            <input type="checkbox" name="bed_type[]" value="Single" class="custom-control-input" id="form-single">
                                                            <label class="custom-control-label" for="form-single">Single</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" name="bed_type[]" value="Double" class="custom-control-input" id="form-double">
                                                            <label class="custom-control-label" for="form-double">Double</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" name="bed_type[]" value="Twin" class="custom-control-input" id="form-twin">
                                                            <label class="custom-control-label" for="form-twin">Twin</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" name="bed_type[]" value="King Size" class="custom-control-input" id="form-king">
                                                            <label class="custom-control-label" for="form-king">King Size</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Bathroom</label>
                                                        <select class="form-control select-no-search" data-fouc data-placeholder="Bathroom type" name="bathroom_type" required>
                                                            <option value="Private">Private</option>
                                                            <option value="Shared">Shared</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Smoking</label>
                                                        <select class="form-control select-no-search" data-fouc data-placeholder="Bathroom type" name="smoking" required>
                                                            <option value="0">Not allowed</option>
                                                            <option value="1">Allowed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Min. Guest</label>
                                                        <input type="number" min="1" max="10" name="min_guest" placeholder="Minimum guest to book this room (1 to 10)" class="form-control" value="1">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Max. Guest</label>
                                                        <input type="number" min="1" max="10" name="max_guest" placeholder="Maximum guest to book this room (1 to 10)" class="form-control" value="10">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Min. Nights</label>
                                                        <input type="number" min="1" name="min_nights" placeholder="Minimum booking nights" class="form-control" value="1">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Max. Nights</label>
                                                        <input type="number" min="1" max="10" name="max_guest" placeholder="Leave empty for no limit" class="form-control">
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
                                                            <input type="number" min="0" max="100" name="limited_threshold" placeholder="Enter number" class="form-control" value="0">
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
                                                        <textarea name="room_short_description" class="form-control" rows="6" placeholder="Room description"></textarea>
                                                    </div>
                                                </div>
                            
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Full Description</label>
                                                        <textarea name="room_description" class="form-control" rows="12" placeholder="Room description"></textarea>
                                                    </div>
                                                </div>
                            
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Room Inclusions</label>
                                                        <textarea name="inclusions" class="form-control" rows="20" placeholder="Room inclusions"></textarea>
                                                    </div>
                                                </div>
                            
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            
                                @can ('update room')
                                    <div class="card-body">
                                        <div class="text-right">
                                            @csrf
                                            <button class="btn bg-danger new-room">Submit</button>
                                        </div>
                                    </div>
                                @endcan
                                <!-- end card body -->
                            
                            </form>

                        </div>
                        
                    </div>
                </div>
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
});
$('.daterange-empty').daterangepicker({
    autoApply: true,
    showDropdowns: true,
    minDate: "01/01/2018",
    minYear: 2018,
    maxYear: 2030,
    autoUpdateInput: true,
    locale: {
        format: 'DD.MM.YYYY'
    }
});
</script>
@endsection