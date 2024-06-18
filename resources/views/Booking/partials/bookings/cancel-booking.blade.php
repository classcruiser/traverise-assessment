<div id="modal_cancel_booking" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-grey-800">
                <h5 class="modal-title">Cancel Booking</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <form action="{{ route('tenant.bookings.cancelBooking', [ 'ref' => $booking->ref ]) }}" class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Cancellation fee</label>
                        <div class="col-sm-2">
                            <input type="number" name="fee" class="form-control form-control-sm" placeholder="0" min="0" max="100" required />
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Cancellation reason</label>
                        <div class="col-sm-9">
                            <textarea name="reason" class="form-control form-control-sm" placeholder="Must be entered" rows="5" required></textarea>
                        </div>
                    </div>
                    
                </div>
                
                <div class="modal-footer">
                    @csrf
                    <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-danger">Proceed</button>
                </div>
                
            </form>
        </div>
    </div>
</div>