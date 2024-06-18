<div id="modal_add_cash_order_{{ $pass->id }}" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-grey-800">
                <h5 class="modal-title">Add Cash Order - {{ $pass->name }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('tenant.classes.multi-pass.cash_order', ['id' => $pass->id]) }}" class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Guest</label>
                        <div class="col-sm-9">
                            <select class="form-control select-remote-data" name="guest_id" data-fouc data-placeholder="Search guest...">
                                <option></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    @csrf
                    <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-danger">Save Order</button>
                </div>

            </form>
        </div>
    </div>
</div>
