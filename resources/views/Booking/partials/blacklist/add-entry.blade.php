<div id="modal-add-blacklist" class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-grey-700">
        <h5 class="modal-title">Add Blacklist entry</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="{{ route('tenant.blacklist.insert') }}" class="form-horizontal" method="post">
        <div class="modal-body">
          <div class="form-group row">
            <label class="col-form-label col-sm-3">First name*</label>
            <div class="col-sm-9">
              <input type="text" name="fname" class="form-control form-control-sm" placeholder="First name" required />
            </div>
          </div>

          <div class="form-group row">
            <label class="col-form-label col-sm-3">Last name</label>
            <div class="col-sm-9">
              <input type="text" name="lname" class="form-control form-control-sm" placeholder="Last name" />
            </div>
          </div>

          <div class="form-group row">
            <label class="col-form-label col-sm-3">Email address*</label>
            <div class="col-sm-9">
              <input type="text" name="email" class="form-control form-control-sm" placeholder="Email address" required />
            </div>
          </div>

          <div class="form-group row">
            <label class="col-form-label col-sm-3">Notes</label>
            <div class="col-sm-9">
              <textarea name="notes" class="form-control form-control-sm" placeholder="Notes about this guest" rows="5"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          {!! csrf_field() !!}
          <button type="button" class="btn btn-link text-slate" data-dismiss="modal">Close</button>
          <button type="submit" class="btn bg-danger">Add Entry</button>
        </div>

      </form>
    </div>
  </div>
</div>