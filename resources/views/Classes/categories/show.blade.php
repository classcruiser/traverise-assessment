@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{ route('tenant.dashboard') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-item"><a href="{{ route('tenant.classes.categories.index') }}" title="" class="text-grey">Class Categories</a></span>
            <span class="breadcrumb-item active">{{ $category->name }}</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h4 class="card-title">{{ $category->name }}</h4>
                    <div class="header-elements">
                        <a href="{{ route('tenant.classes.categories.index') }}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <div class="tab-content">
                        <div class="tab-pane active">

                            <form action="{{ route('tenant.classes.categories.update', ['id' => $category->id]) }}" method="POST">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4"><h6>Details</h6></div>
                                        <div class="col-sm-8">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>* Name</label>
                                                        <input type="text" name="name" placeholder="Name" class="form-control @error('name') is-invalid @enderror" maxlength="255" required value="{{ old('name', $category->name) }}">
                                                        @error('name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Short Name</label>
                                                        <input type="text" name="short_name" placeholder="Short Name" class="form-control @error('short_name') is-invalid @enderror" maxlength="255" value="{{ old('short_name', $category->short_name) }}">
                                                        @error('short_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Description</label>
                                                        <textarea name="description" class="frl form-control">{!! $category->description !!}</textarea>
                                                    </div>
                                                </div>

                                                <div class="form-group col-sm-12">
                                                    <label class="col-form-label ">Available Weeks in Calendar</label>
                                                    <div >
                                                        <select class="form-control" name="weeks">
                                                            <option value="2">Select count of weeks</option>
                                                            @for($i = 1; $i <= 12;$i++)
                                                                <option value="{{$i}}" @if($category->weeks == $i) selected @endif >{{$i}}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="form-active" @checked(old('is_active', $category->is_active))>
                                                            <label class="custom-control-label" for="form-active">Active</label>
                                                        </div>
                                                        <div class="font-size-sm text-muted">
                                                            Inactive category are not displayed in booking process
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="is_shop" value="1" class="custom-control-input" id="form-is_shop" @checked(old('is_shop', $category->is_shop))>
                                                            <label class="custom-control-label" for="form-is_shop">Show in shop</label>
                                                        </div>
                                                        <div class="font-size-sm text-muted">
                                                            Check this to show this category in shop
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="booker_only" value="1" class="custom-control-input" id="form-booker_only" @checked(old('booker_only', $category->booker_only))>
                                                            <label class="custom-control-label" for="form-booker_only">Booker Only</label>
                                                        </div>
                                                        <div class="font-size-sm text-muted">
                                                            When enabled, guest will only need to fill the booker details.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4"><h6>Block Schedules</h6></div>
                                        <div class="col-sm-8">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <table class="table table-xs table-compact">
                                                        <thead>
                                                            <tr class="bg-grey-400">
                                                                <th>Day</th>
                                                                <th>Date</th>
                                                                <th>Time</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($category->blocks as $block)
                                                                <tr>
                                                                    <td>{{ $block->day }}</td>
                                                                    <td>
                                                                        @if ($block->date)
                                                                            {{ $block->date->format('d.m.Y') }}
                                                                        @else
                                                                            --
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($block->is_wholeday)
                                                                            <span>Last for the whole day</span>
                                                                        @else
                                                                            {{ $block->start_formatted }} - {{ $block->end_formatted }}
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-right">
                                                                        <div class="list-icons">
                                                                            <a href="javascript:void(0)" class="list-icons-item tippy" data-tippy-content="Edit"  data-toggle="modal" data-target="{!! $block->is_day_off ? "#modal_edit_day_off_block_{$block->id}" : "#modal_edit_block_{$block->id}" !!}"><i class="icon-pencil7"></i></a>
                                                                            <a href="{{ route('tenant.classes.categories.schedules.destroy', ['id' =>  $category->id, 'scheduleId' => $block->id]) }}" class="list-icons-item text-danger confirm-dialog tippy" data-tippy-content="Delete"  data-text="Delete this block?"><i class="icon-trash"></i></a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-sm-4">
                                                    <a href="javascript:void(0)" class="btn btn-sm btn-block bg-danger-400" data-toggle="modal" data-target="#modal_add_block"><i class="fa fa-fw fa-calendar"></i> Add Block Schedule</a>
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
@include('Classes.partials.category.add-block')
@foreach ($category->blocks as $schedule)
    @include('Classes.partials.category.edit-block')
@endforeach

@endsection

@section('scripts')
    <script>
    $('.date-time-picker').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        autoApply: true,
        minDate: '{{ today()->format("d/m/Y") }}',
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
    $(document).ready(function() {
        $('textarea.frl').froalaEditor({
            charCounterCount: false,
            toolbarButtons: ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'insertLink', 'insertHR', 'insertTable', 'html'],
            tableStyles: {
                'payment-email': 'payment-email'
            },
            heightMin: 400,
            heightMax: 800
        })
    });
    </script>
@endsection
