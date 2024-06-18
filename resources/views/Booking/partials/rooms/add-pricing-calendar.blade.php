<div id="modal_add_pricing_calendar" class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-grey-700">
        <h5 class="modal-title"><i class="icon-pencil5 mr-1"></i> Update Prices</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="{{ route('tenant.rooms.updateCalendarPrice',  [ 'id' => $room->id ]) }}" class="form-horizontal" method="post">
        <div class="modal-body">
          <div class="form-group row">
            <label class="col-form-label col-sm-3">Date range</label>
            <div class="col-sm-9 align-self-center">
              <b><span id="add-pricing-date-range"></span></b>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-form-label col-sm-3">Season type</label>
            <div class="col-sm-9">
              <select class="form-control select-no-search" name="season_type">
                <option value="LOW">Low season</option>
                <option value="MAIN">Main season</option>
                <option value="PEAK">Peak season</option>
                <option value="SPECIAL">Special season</option>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-form-label col-sm-3">Price</label>
            <div class="col-sm-9">
              <div class="input-group input-group-sm" style="width: 200px">
                <span class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-fw fa-euro-sign"></i></span>
                </span>
                <input type="text" name="price" class="form-control form-control-sm" placeholder="0.0" required style="width: 80px" required />
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          {!! csrf_field() !!}
          <input type="hidden" name="dates" id="add-pricing-dates" />
          <button type="button" class="btn btn-link text-slate" data-dismiss="modal">Close</button>
          <button type="submit" class="btn bg-danger">Update Price</button>
        </div>

      </form>
    </div>
  </div>
</div>