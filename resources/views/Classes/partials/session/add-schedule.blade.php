<div id="modal_add_schedule" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-grey-800">
                <h5 class="modal-title">Add Schedule</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('tenant.classes.sessions.schedules.store', ['id' => $record->id]) }}" class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Day</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="day">
                                <option value="MON">Monday</option>
                                <option value="TUE">Tuesday</option>
                                <option value="WED">Wednesday</option>
                                <option value="THU">Thursday</option>
                                <option value="FRI">Friday</option>
                                <option value="SAT">Saturday</option>
                                <option value="SUN">Sunday</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Date</label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-sm" style="width: 270px">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-calendar-alt"></i></span>
                                </span>
                                <input type="text" class="form-control date-time-picker" name="date" autocomplete="off" placeholder="Optional">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Duration in Weeks</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="weeks">
                                <option value="">Count weeks</option>
                                @for($i = 1; $i <= 12;$i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
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
                        <label class="col-form-label col-sm-3">Instructor</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="s_instructor_id">
                                <option value="">-- Please Select --</option>
                                @foreach ($instructors as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Max Pax</label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-sm" style="width: 270px">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                                </span>
                                <input type="text" name="s_max_pax" class="form-control form-control-sm" placeholder="1" style="width: 80px" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Price</label>
                        <div class="col-sm-9">
                            <div class="input-group input-group-sm" style="width: 270px">
                                <span class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-euro-sign"></i></span>
                                </span>
                                <input type="text" name="s_price" class="form-control form-control-sm" placeholder="0.0" style="width: 80px" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Active</label>
                        <div class="col-sm-9">
                            <div class="col-form-label custom-control custom-checkbox">
                                <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="schedule-active" checked="checked" >
                                <label class="custom-control-label" for="schedule-active"></label>
                            </div>
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
