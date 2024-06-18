<div id="modal_approve_booking" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('tenant.classes.bookings.approve', ['ref' => $booking->ref]) }}" method="POST" id="form_approve_booking">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-grey-800">
                    <h5 class="modal-title">Approve Booking</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-3 vertical-top">Options</label>
                        <div class="col-sm-9">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="email" class="custom-control-input" id="form-approve-email">
                                <label class="custom-control-label" for="form-approve-email">Send Confirmation Email to Guest</label>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn bg-danger" data-ref="{{$booking->ref}}">Approve</button>
                </div>

            </div>
        </form>
    </div>
</div>