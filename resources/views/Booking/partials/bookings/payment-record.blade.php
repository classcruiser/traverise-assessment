<div id="modal_payment_record" class="modal fade" tabindex="-1">
    <div class="modal-dialog" v-if="payment.record">
        <div class="modal-content">
            <div class="modal-header bg-grey-800">
                <h5 class="modal-title">Payment Record</h5>
                <button type="button" class="close" data-dismiss="modal" v-on:click="closeModal()">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-4 text-uppercase">Methods</div>
                    <div class="col-8">: <b>@{{payment.record.methods.toUpperCase()}}</b></div>
                </div>
                <div v-if="payment.record.methods == 'banktransfer'">
                    <div class="form-group row">
                        <div class="col-4 text-uppercase">Bank Name</div>
                        <div class="col-8">: <b>@{{payment.record.bank_name}}</b></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-4 text-uppercase">Account Number</div>
                        <div class="col-8">: <b>@{{payment.record.account_number}}</b></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-4 text-uppercase">Account Name</div>
                        <div class="col-8">: <b>@{{payment.record.account_owner}}</b></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-4 text-uppercase">IBAN code</div>
                        <div class="col-8">: <b>@{{payment.record.iban_code}}</b></div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-4 text-uppercase">Paid at</div>
                    <div class="col-8">: <b>@{{payment.record.paid_at}}</b></div>
                </div>
                <div class="form-group row" v-if="payment.record.methods != 'paypal'">
                    <div class="col-4 text-uppercase">Transfer proof</div>
                    <div class="col-8"><a :href="payment.record.proof" target="_blank"><img :src="payment.record.proof" class="img-fluid" /></a></div>
                </div>
                <div class="form-group row">
                    <div class="col-4 text-uppercase">Deposit Amount</div>
                    <div class="col-8">: <b>&euro;@{{payment.record.deposit_total}}</b></div>
                </div>
                <div class="form-group row">
                    <div class="col-4 text-uppercase">Grand Total</div>
                    <div class="col-8">: <b>&euro;@{{payment.record.grand_total}}</b></div>
                </div>
                <div class="form-group row">
                    <div class="col-4 text-uppercase">Amount</div>
                    <div class="col-8">
                        <div class="input-group input-group-sm" style="width: 120px">
                            <span class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-euro-sign"></i></span>
                            </span>
                            <input type="text" name="amount" class="form-control form-control-sm" v-model="payment.amount" required />
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                @csrf
                <button type="button" class="btn btn-link text-danger" data-dismiss="modal" v-on:click="closeModal()">Close</button>
                <button type="submit" class="btn bg-danger btn-verify-payment" v-on:click="verifyPayment(payment.record.id)" :disabled="payment.verify_button_text != 'Verify Payment'">
                    <span v-html="payment.verify_button_text"></span>
                </button>
            </div>

        </div>
    </div>
</div>