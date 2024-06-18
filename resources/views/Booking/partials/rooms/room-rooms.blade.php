<form action="javascript:" method="post" id="room-subrooms">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-4"><h6>Room List</h6></div>
            <div class="col-sm-8">
                <div class="row">
                    
                    @can ('save setting')
                        <div class="col-sm-12 text-right mb-1">
                            <a href="#" class="alpha-success text-success-800 btn add-subroom" data-capacity="{{ $room->total_capacity }}">
                                <i class="fal fa-plus"></i> Add Room
                            </a>
                        </div>
                    @endcan
                    
                    <div class="col-sm-12" id="subrooms">
                        @if ($room->rooms->count() > 0)
                            @foreach ($room->rooms as $subroom)
                                <div class="py-2 border-bottom-1 border-alpha-grey" id="key-{{ $subroom->id }}">
                                    <div class="d-flex justify-content-start align-items-center">
                                        
                                        <div class="mr-1">
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">NAME</span>
                                                </span>
                                                <input type="text" class="form-control" placeholder="Name" name="subroom[{{ $subroom->id }}][name]" value="{{ $subroom->name }}" required />
                                            </div>
                                        </div>
                                        
                                        <div class="ml-auto" style="width: 145px;">
                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">BED</span>
                                                </span>
                                                <input type="number" class="form-control" placeholder="1" name="subroom[{{ $subroom->id }}][beds]" value="{{ $subroom->beds }}" required />
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            @endforeach
                        @endif
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
                <button class="btn bg-danger update-room-subrooms">Update Rooms</button>
            </div>
        </div>
    @endcan
    <!-- end card body -->
    
</form>
