<div id="modal-edit-blacklist" class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-grey-700">
        <h5 class="modal-title">Edit Blacklist entry</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="/blacklist/update" class="form-horizontal" method="post">
        <div class="modal-body">
          <div class="form-group row">
            <label class="col-form-label col-sm-3">First name*</label>
            <div class="col-sm-9">
              <input type="text" name="fname" id="list-fname" class="form-control form-control-sm" placeholder="First name" required />
            </div>
          </div>

          <div class="form-group row">
            <label class="col-form-label col-sm-3">Last name</label>
            <div class="col-sm-9">
              <input type="text" name="lname" id="list-lname" class="form-control form-control-sm" placeholder="Last name" />
            </div>
          </div>

          <div class="form-group row">
            <label class="col-form-label col-sm-3">Email address*</label>
            <div class="col-sm-9">
              <input type="text" name="email" id="list-email" class="form-control form-control-sm" placeholder="Email address" required />
            </div>
          </div>

          <div class="form-group row">
            <label class="col-form-label col-sm-3">Notes</label>
            <div class="col-sm-9">
              <textarea name="notes" id="list-notes" class="form-control form-control-sm" placeholder="Notes about this guest" rows="5"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <input type="hidden" name="id" id="list-id" value="" />
          <button type="button" class="btn btn-link text-slate" data-dismiss="modal">Close</button>
          @if (Auth::user()->role_id == 1)
            {!! csrf_field() !!}
            <button type="submit" class="btn bg-danger">Update Entry</button>
          @endif
        </div>

      </form>
    </div>
  </div>
</div>