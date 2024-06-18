<div id="modal_add_day_off_block" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-grey-800">
                <h5 class="modal-title">Add Day Off Block Schedule</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('tenant.classes.categories.day_off_schedules.store', ['id' => $category->id]) }}" class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Date</label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-sm" style="width: 270px">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-calendar-alt"></i></span>
                                </span>
                                <input type="text" class="form-control date-time-picker" name="date" autocomplete="off" placeholder="Schedule Date" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Start</label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-sm" style="width: 270px">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-clock"></i></span>
                                </span>
                                <input type="text" class="form-control date-time-only" name="start" autocomplete="off" placeholder="Start time">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">End</label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-sm" style="width: 270px">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-clock"></i></span>
                                </span>
                                <input type="text" class="form-control date-time-only" name="end" autocomplete="off" placeholder="End time">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Sessions</label>
                        <div class="col-sm-9">
                            <select class="form-select select" name="sessions[]" multiple="multiple">
                                @foreach ($classes as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">&nbsp;</label>
                        <div class="col-sm-9">
                            <label>
                                <input type="checkbox" name="is_wholeday" class="mr-1" />
                                <span>is whole day</span>
                            </label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    @csrf
                    <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-danger">Add Schedule</button>
                </div>

            </form>
        </div>
    </div>
</div>
