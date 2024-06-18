<div id="modal_add_transfer" class="modal fade" tabindex="-1" data-focus="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-grey-800">
                <h5 class="modal-title">Add Transfer</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <form action="{{ route('tenant.bookings.addTransfer', [ 'ref' => $booking->ref ]) }}" class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Transfer Add-on</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="transfer_id">
                                @foreach ($transfers as $transfer)
                                    <option value="{{ $transfer->id }}">{{ $transfer->name }} ({{ $transfer->direction }})</option>
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
                                <input type="text" name="flight_number" class="form-control form-control-sm" required />
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
                                <input type="text" class="form-control date-time-picker" name="flight_time" autocomplete="off"> 
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
                                <input type="text" name="price" class="form-control form-control-sm" placeholder="0.0" required style="width: 80px" />
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
                                <input type="text" name="guests" class="form-control form-control-sm" placeholder="1" required style="width: 80px" />
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <div class="modal-footer">
                    @csrf
                    <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Close</button>
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}" />
                    <button type="submit" class="btn bg-danger">Add Transfer</button>
                </div>
                
            </form>
        </div>
    </div>
</div>