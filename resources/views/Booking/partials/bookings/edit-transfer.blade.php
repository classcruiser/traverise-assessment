<div id="{{ $modal_id }}" class="modal fade" tabindex="-1" data-focus="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-grey-800">
                <h5 class="modal-title">Edit Transfer: {{ $transfer->details->direction }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <form action="{{ route('tenant.bookings.updateTransfer', [ 'ref' => $booking->ref, 'booking_transfer_id' => $transfer->id ]) }}" class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Transfer Add-on</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="transfer_id">
                                @foreach ($transfers as $transfer_addon)
                                    <option value="{{ $transfer_addon->id }}" @selected($transfer_addon->id == $transfer->transfer_extra_id)>{{ $transfer_addon->name }} ({{ $transfer_addon->direction }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Flight number</label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-sm">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-plane"></i></span>
                                </span>
                                <input type="text" name="flight_number" class="form-control form-control-sm" required value="{{ $transfer->flight_number }}" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Flight time</label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-sm" style="width: 200px">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-calendar-alt"></i></span>
                                </span>
                                <input type="text" class="form-control date-time-picker" name="flight_time" value="{{ $transfer->flight_time ? $transfer->flight_time->format('d.m.Y H:i') : '' }}" autocomplete="off"> 
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Price</label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-sm" style="width: 200px">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-euro-sign"></i></span>
                                </span>
                                <input type="text" name="price" class="form-control form-control-sm" placeholder="0.0" required style="width: 80px" value="{{ $transfer->price }}" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Guest</label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-sm" style="width: 200px">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                                </span>
                                <input type="text" name="guests" class="form-control form-control-sm" placeholder="1" required style="width: 80px" value="{{ $transfer->guests }}" />
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <div class="modal-footer">
                    @csrf
                    <a href="{{ route('tenant.bookings.removeTransfer', [ 'ref' => $booking->ref, 'booking_transfer_id' => $transfer->id ]) }}" title="" class="mr-auto btn bg-grey-300 confirm-dialog" data-text="Remove transfer ?">Remove Transfer</a>
                    <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-danger">Update Transfer</button>
                </div>
                
            </form>
        </div>
    </div>
</div>