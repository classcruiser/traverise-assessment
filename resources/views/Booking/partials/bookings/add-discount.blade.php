<div id="modal_add_discount" class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-grey-800">
        <h5 class="modal-title">Add Discount</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="{{ route('tenant.bookings.addDiscount', [ 'ref' => $booking->ref ]) }}" class="form-horizontal" method="post">
        <div class="modal-body">
          <div class="form-group row">
            <label class="col-form-label col-sm-3">Discount name</label>
            <div class="col-sm-9">
              <input type="text" name="name" class="form-control form-control-sm" placeholder="Name / description" />
            </div>
          </div>

          <div class="form-group row">
            <label class="col-form-label col-sm-3">Discount type</label>
            <div class="col-sm-9">
              <select class="form-control select-no-search" name="discount_type">
                <option value="Percent">Percent</option>
                <option value="Fixed">Fixed</option>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-form-label col-sm-3">Apply to</label>
            <div class="col-sm-9">
              <select class="form-control select-no-search" name="apply_to">
                <option value="ROOM">Room price only</option>
                <option value="ALL">Full price</option>
              </select>
              <div class="form-text text-muted">Only if you select Percent discount type</div>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-form-label col-sm-3">Discount value</label>
            <div class="col-sm-9">
              <input type="text" name="discount_value" class="form-control form-control-sm" placeholder="0.0" required style="width: 80px" />
            </div>
          </div>

        </div>

        <div class="modal-footer">
          {!! csrf_field() !!}
          <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Close</button>
          <button type="submit" class="btn bg-danger">Add Discount</button>
        </div>

      </form>
    </div>
  </div>
</div>