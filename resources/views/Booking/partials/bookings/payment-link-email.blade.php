<div id="modal_payment_link_email" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-grey-800">
                <h5 class="modal-title">Payment link email</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-form-label col-sm-3">Email</label>
                    <div class="col-sm-9">
                        <input type="email" name="email-payment-link" class="form-control form-control-sm" placeholder="email@email.com" />
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Close</button>
                <button type="submit" class="btn bg-danger" @click="sendPaymentLink({{$booking->id}})">Send</button>
            </div>
        </div>
    </div>
</div>
