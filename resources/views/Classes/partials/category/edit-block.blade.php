<div id="modal_edit_block_{{ $schedule->id }}" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-grey-800">
                <h5 class="modal-title">Edit Schedule</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('tenant.classes.categories.schedules.update', ['id' => $category->id, 'scheduleId' => $schedule->id]) }}" class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Day</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="day">
                                <option value="MON" @selected($schedule->day == 'MON')>Monday</option>
                                <option value="TUE" @selected($schedule->day == 'TUE')>Tuesday</option>
                                <option value="WED" @selected($schedule->day == 'WED')>Wednesday</option>
                                <option value="THU" @selected($schedule->day == 'THU')>Thursday</option>
                                <option value="FRI" @selected($schedule->day == 'FRI')>Friday</option>
                                <option value="SAT" @selected($schedule->day == 'SAT')>Saturday</option>
                                <option value="SUN" @selected($schedule->day == 'SUN')>Sunday</option>
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
                                <input type="text" class="form-control date-time-picker" name="date" value="{{ $schedule->date?->format('d.m.Y') }}" autocomplete="off" placeholder="Optional">
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
                                <input type="text" class="form-control date-time-only" name="start" autocomplete="off" value="{{ $schedule->start }}" placeholder="Start time">
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
                                <input type="text" class="form-control date-time-only" name="end" autocomplete="off" value="{{ $schedule->end }}" placeholder="End time">
                            </div>
                        </div>
                    </div>

                    @php
                        $scheduleSessions = $schedule->sessions ? $schedule->sessions->pluck('id')->all() : [];
                    @endphp

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">Sessions</label>
                        <div class="col-sm-9">
                            <select class="form-select select" name="sessions[]" multiple="multiple">
                                @foreach ($classes as $key => $value)
                                    <option value="{{ $key }}" {{ in_array($key, $scheduleSessions) ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3">&nbsp;</label>
                        <div class="col-sm-9">
                            <label>
                                <input type="checkbox" name="is_wholeday" class="mr-1" @checked($schedule->is_wholeday) />
                                <span>is whole day</span>
                            </label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    @csrf
                    @method('PUT')
                    <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-danger">Save Schedule</button>
                </div>

            </form>
        </div>
    </div>
</div>
