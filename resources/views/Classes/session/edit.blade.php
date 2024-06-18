@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item"><a href="{{ route('tenant.classes.sessions.index') }}" title="" class="text-grey">Class Sessions</a></span>
            <span class="breadcrumb-item active">{{ $record->name }}</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h4 class="card-title">{{ $record->name }}</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.classes.sessions.index') }}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <div class="tab-content">
                        <div class="tab-pane active">

                            <form action="{{ route('tenant.classes.sessions.update', ['id' => $record->id]) }}" method="POST">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3"><h6>Details</h6></div>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Name</label>
                                                        <input type="text" name="name" placeholder="Name" class="form-control @error('name') is-invalid @enderror" maxlength="255" required value="{{ old('name', $record->name) }}">
                                                        @error('name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Color</label>
                                                        <input data-jscolor="{ preset: 'dark', closeButton: true, closeText: 'OK' }" value="{{ old('color', $record->color) }}" name="color" required class="form-control @error('color') is-invalid @enderror" />
                                                        @error('color')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Category</label>
                                                        <select class="form-control select-no-search" data-fouc data-placeholder="Category" name="class_category_id" required>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}" @selected(old('class_category_id', $record->class_category_id) == $category->id)>{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6"></div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="form-active" @checked(old('is_active', $record->is_active))>
                                                            <label class="custom-control-label" for="form-active">Active ?</label>
                                                        </div>
                                                        <div class="font-size-sm text-muted">
                                                            Inactive class session are not displayed in booking process
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3"><h6>Properties</h6></div>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Instructor</label>
                                                        <select class="form-control select-no-search" data-fouc name="instructor_id">
                                                            <option value="">-- Please Select --</option>
                                                            @foreach ($instructors as $key => $value)
                                                                <option value="{{ $key }}" @selected(old('instructor_id', $record->instructor_id) == $key)>{{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Max. Guest</label>
                                                        <input type="number" min="1" max="20" name="max_pax" placeholder="Maximum guest to book this class (1 to 20)" class="form-control @error('max_pax') is-invalid @enderror" value="{{ old('max_pax', $record->max_pax) }}" required>
                                                        @error('max_pax')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3"><h6>Base Pricing</h6></div>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Default price</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend">
                                                                <span class="input-group-text">&euro;</span>
                                                            </span>
                                                            <input type="text" name="price" class="form-control @error('price') is-invalid @enderror" required value="{{ old('price', $record->price) }}" />
                                                            @error('price')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body {{ request()->has('mass-update') ? '' : 'd-none' }}">
                                    <div class="row">
                                        <div class="col-sm-3"><h6>Mass Update Weeks</h6></div>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Day's schedule</label>
                                                        <select class="form-control select-no-search" data-fouc id="mass-update-day">
                                                            <option value="">Select day</option>
                                                            <option value="All">All</option>
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
                                                <div class="col-sm-5">
                                                    <div class="form-group">
                                                        <label>Duration Week</label>
                                                        <select class="form-control select-no-search" data-fouc id="mass-update-week">
                                                            <option value="">Select week</option>
                                                            @for ($i = 1; $i <= 12; $i++)
                                                                <option value="{{ $i }}">{{ $i }} {{ Str::plural('week', intVal($i)) }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-1">
                                                    <label>&nbsp;</label>
                                                    <button name="mass-update" class="btn bg-grey-800" id="mass-update-button" data-id="{{ $record->id }}">GO</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12"><h6>Schedules</h6></div>
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <table class="table table-xs table-compact">
                                                        <thead>
                                                            <tr class="bg-grey-400">
                                                                <th>Day</th>
                                                                <th class="text-center">Weeks</th>
                                                                <th>Until</th>
                                                                <th>Date</th>
                                                                <th>Time</th>
                                                                <th>Instructor</th>
                                                                <th>Max Pax</th>
                                                                <th>Price</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($record->schedules as $schedule)
                                                                <tr>
                                                                    <td>{{ $schedule->day }}</td>
                                                                    <td class="text-center">{{ $schedule->weeks }}</td>
                                                                    <td>{{ $schedule->weeks ? \Carbon\Carbon::parse($schedule->until)->format('d.m.Y') : '' }}</td>
                                                                    <td>
                                                                        @if ($schedule->date)
                                                                            {{ $schedule->date->format('d.m.Y') }}
                                                                        @else
                                                                            --
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $schedule->start_formatted }} - {{ $schedule->end_formatted }}</td>
                                                                    <td>{{ $schedule->instructor?->name ?? '--' }}</td>
                                                                    <td>
                                                                        @if ($schedule->max_pax)
                                                                            {{ $schedule->max_pax }} <i class="far fa-fw fa-users"></i>
                                                                        @else
                                                                            --
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($schedule->price)
                                                                            <b>&euro;{{ $schedule->price }}</b>
                                                                        @else
                                                                            --
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-right">
                                                                        <div class="list-icons">
                                                                            <a href="javascript:void(0)" class="list-icons-item toggle-status" data-schedule_id="{{$schedule->id}}">
                                                                                @if($schedule->is_active)
                                                                                    <i class="far fa-fw fa-square-check text-success"></i>
                                                                                @else
                                                                                    <i class="fal fa-fw fa-rectangle-xmark text-danger"></i>
                                                                                @endif
                                                                            </a>
                                                                            <a href="javascript:void(0)" class="list-icons-item tippy" data-tippy-content="Edit"  data-toggle="modal" data-target="#modal_edit_schedule_{{ $schedule->id }}"><i class="icon-pencil7"></i></a>
                                                                            <a href="javascript:void(0)" class="list-icons-item text-slate tippy" data-tippy-content="Duplicate" data-toggle="modal" data-target="#modal_duplicate_schedule_{{ $schedule->id }}"><i class="icon-files-empty2"></i></a>
                                                                            <a href="{{ route('tenant.classes.sessions.schedules.destroy', ['id' =>  $record->id, 'scheduleId' => $schedule->id]) }}" class="list-icons-item text-danger confirm-dialog tippy" data-tippy-content="Delete"  data-text="Delete this schedule?"><i class="icon-trash"></i></a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="d-flex justify-content-center col-sm-12">
                                                    <div class="mr-2">
                                                        <a href="javascript:void(0)" class="btn btn-sm bg-danger-400" data-toggle="modal" data-target="#modal_add_schedule">
                                                            <i class="fa fa-fw fa-calendar"></i> Add Schedule
                                                        </a>
                                                    </div>
                                                    <a href="{{route('tenant.classes.sessions.calendar', $record->id)}}" class="btn btn-sm bg-danger-400" >
                                                        <i class="fa fa-fw fa-calendar-days"></i> View Calendar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="text-right">
                                        @csrf
                                        @method('PUT')
                                        <button class="btn bg-danger new-room">Submit</button>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Classes.partials.session.add-schedule')

@foreach ($record->schedules as $schedule)
    @include('Classes.partials.session.edit-schedule')
    @include('Classes.partials.session.duplicate-schedule')
@endforeach

@endsection

@section('scripts')
    <script>
    $('.date-time-picker').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        autoApply: true,
        locale: {
            cancelLabel: 'Clear',
            format: 'DD.MM.YYYY'
        }
    });

    $('.date-time-picker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD.MM.YYYY'));
    });

    $('.date-time-only').daterangepicker({
        timePicker : true,
        singleDatePicker:true,
        timePicker24Hour : true,
        timePickerIncrement : 5,
        locale : {
            format : 'HH:mm:ss'
        }
    }).on('show.daterangepicker', function(ev, picker) {
        picker.container.find(".calendar-table").hide();
    });

    tippy('.tippy', {
        content: 'Tooltip',
        arrow: true,
    });

    $('.toggle-status').click(function () {
        let elem = this;
        let id = $(elem).data('schedule_id');

        axios
            .put(route('tenant.classes.sessions.schedule.status-toggle', id ))
            .then((res) => {
                let i = $(elem).find('i');

                if (res.data.is_active) {
                    i.removeClass('fa-rectangle-xmark text-danger')
                        .addClass('fa-square-check text-success');
                } else {
                    i.removeClass('fa-square-check text-success')
                        .addClass('fa-rectangle-xmark text-danger');
                }

                $(`#schedule-active_${id}`).prop('checked', Boolean(res.data.is_active));
                $(`#dublicate_schedule-active_${id}`).prop('checked', Boolean(res.data.is_active));
            })
            .catch(err => {
                console.log(err);
            });

    })

    </script>
@endsection
