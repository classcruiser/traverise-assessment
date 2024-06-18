@extends('Booking.app')

@section('content')

    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <span class="breadcrumb-item"><a href="{{route('tenant.automated-emails')}}" title="" class="text-grey">Automated Emails</a></span>
                <span class="breadcrumb-item active">{{$email->name}}</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                <div class="mb-2 py-1 px-2 d-flex justify-content-end align-items-center">
                    <a href="{{ route('tenant.automated-emails.preview-recipient', ['id' => $id]) }}" title="" class="btn bg-secondary" target="_blank">
                        Preview Recipient
                    </a>
                </div>
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h4 class="card-title">{{$email->name}}</h4>
                        <div class="header-elements">
                            <a href="{{route('tenant.automated-emails')}}" title="" class="btn btn-link text-danger">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>
                    <form action="{{route('tenant.automated-emails.update', [ 'id' => $id ])}}" method="post">
                        <div class="card-body border-top-1 border-alpha-grey pt-3">
                            @include('Booking.partials.form-messages')
                            <div class="row">
                                <div class="col-sm-4">
                                    <h6>Details</h6>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>* Name</label>
                                                <input type="text" name="name" placeholder="Name" class="form-control" value="{{$email->name}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Subject</label>
                                                <input type="text" name="subject" placeholder="Subject" class="form-control" value="{{$email->subject}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Slug</label>
                                                <input type="text" name="slug" readonly class="form-control" value="{{$email->slug}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label>Time value</label>
                                                <input type="number" name="send_time" min="1" placeholder="1" class="form-control" value="{{$email->send_time}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Time unit</label>
                                                <select class="form-control select-no-search" data-fouc data-placeholder="Time unit" name="time_unit">
                                                    <option value="minutes" {{$email->time_unit == 'minutes' ? 'selected' : ''}}>Minutes</option>
                                                    <option value="hours" {{$email->time_unit == 'hours' ? 'selected' : ''}}>Hours</option>
                                                    <option value="days" {{$email->time_unit == 'days' ? 'selected' : ''}}>Days</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Timing</label>
                                                <select class="form-control select-no-search" data-fouc data-placeholder="Send timing" name="send_timing">
                                                    <option value="BEFORE" {{$email->send_timing == 'BEFORE' ? 'selected' : ''}}>BEFORE</option>
                                                    <option value="AFTER" {{$email->send_timing == 'AFTER' ? 'selected' : ''}}>AFTER</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Column Name</label>
                                                <select class="form-control select-no-search" data-fouc data-placeholder="Send timing" name="send_date_column">
                                                    @foreach($columns as $parent_column)
                                                        <optgroup label="{{$parent_column['name']}}">
                                                            @foreach($parent_column['columns'] as $column => $column_label)
                                                                <option value="{{$column}}" {{$email->send_date_column == $column ? 'selected' : ''}}>&raquo; {{$column_label}}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6"></div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Template</label>
                                                <textarea name="resource" class="form-control frl" rows="20">{!! $template !!}</textarea>

                                                <div class="text-right hidden">
                                                    <a href="#" title="" class="d-inline-block mt-2">Preview template</a>
                                                </div>
                                            </div>
                                            <div class="notice-box text-sm p-2 mb-2">
                                                <p>You can use below code in your template:</p>
                                                <table class="table border table-xs">
                                                    <tr>
                                                        <td width="50%"><code>{guest_name}</code></td>
                                                        <td>guest full name</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>{ref}</code></td>
                                                        <td>booking reference number</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>{camp}</code></td>
                                                        <td>selected camp / location</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>{check_in}</code></td>
                                                        <td>check in date</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>{check_out}</code></td>
                                                        <td>check out date</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>{open_balance}</code></td>
                                                        <td>open balance amount</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>{deposit_due_date}</code></td>
                                                        <td>deposit due date</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>{deposit_amount}</code></td>
                                                        <td>deposit amount</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>{payment_link}</code></td>
                                                        <td>payment page link</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Is Scheduled?</label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="is_scheduled" class="custom-control-input" id="form-active" {{$email->is_scheduled ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="form-active">Email will be sent automatically when the condition matches</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <h6>Conditions</h6>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Column</label>
                                                <select class="form-control select-no-search" data-fouc data-placeholder="Select column" name="condition_column">
                                                    <option></option>
                                                    @foreach($condition_columns as $key => $parent)
                                                        <optgroup label="{{strtoupper($key)}}">
                                                            @foreach($parent as $key => $value)
                                                                <option value="{{$key}}" {{$key == $email->condition?->column ? 'selected' : ''}}>&raquo; {{$value}}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Operator</label>
                                                <select class="form-control select-no-search" data-fouc data-placeholder="Select operator" name="condition_operator">
                                                    <option></option>
                                                    @foreach($operators as $key => $operator)
                                                        <option value="{{$key}}" {{$key == $email->condition?->operator ? 'selected' : ''}}>&raquo; {{$operator}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Value</label>
                                                <input type="text" name="condition_value" placeholder="Enter value" class="form-control" value="{{$email->condition?->value}}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <h6>Add ons trigger</h6>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <label>Send email when booking contains the selected addon(s) is placed</label>
                                                    <span></span>
                                                </div>

                                                <div class="border-1 border-alpha-grey">
                                                    @foreach($addons as $addon)
                                                        <div class="py-2 px-3 border-bottom-1 border-alpha-grey">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input checkbox-addon checkbox-{{$addon->id}}" id="addon-{{$addon->id}}" name="addons[{{$addon->id}}]" {{$email->addons->contains('extra_id', $addon->id) ? 'checked' : ''}}>
                                                                <label class="custom-control-label" for="addon-{{$addon->id}}">{{$addon->name}}</label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <h6>Room associations</h6>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="border-1 border-alpha-grey">
                                                    @foreach($locations as $location)
                                                        <div class="py-2 px-3 alpha-grey {{$loop->last ? '' : 'border-bottom-1 border-alpha-grey'}}"><a href="javascript:" onClick="$('.checkbox-{{$location->id}}').attr('checked', true)" class="text-danger"><i class="fa fa-fw fa-home mr-1"></i> <b>{{$location->name}}</b></a></div>
                                                        @foreach($location->rooms as $room)
                                                            <div class=" py-2 px-3 border-bottom-1 border-alpha-grey">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" class="custom-control-input checkbox-{{$location->id}}" id="room-{{$room->id}}" name="rooms[{{$room->id}}]" {{$email->rooms->contains('room_id', $room->id) ? 'checked' : ''}}>
                                                                    <label class="custom-control-label" for="room-{{$room->id}}">{{$room->name}}</label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <h6>Attached documents</h6>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="border-1 border-alpha-grey">
                                                    @foreach($documents as $document)
                                                        <div class=" py-2 px-3 border-bottom-1 border-alpha-grey">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="document-{{$document->id}}" name="documents[{{$document->id}}]" {{$email->documents?->contains('document_id', $document->id) ? 'checked' : ''}}>
                                                                <label class="custom-control-label" for="document-{{$document->id}}">{{$document->name}}</label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>

                        @can('edit automated email')
                            <div class="card-body">
                                <div class="text-right">
                                    @csrf
                                    <input type="hidden" id="email_id" value="{{$email->id}}" />
                                    <input type="hidden" name="old_rooms" value="{{json_encode($email->rooms)}}" />
                                    <input type="hidden" name="old_documents" value="{{json_encode($email->documents)}}" />
                                    <input type="hidden" name="old_addons" value="{{json_encode($email->addons)}}" />
                                    <button class="btn bg-danger" type="submit">Update Template</button>
                                </div>
                            </div>
                        @endcan
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
    <script>
    tippy('.tippy', {
        content: 'Tooltip',
        arrow: true,
    });
    $('.daterange-empty').daterangepicker({
        autoApply: true,
        showDropdowns: true,
        minDate: "01/01/2018",
        minYear: 2018,
        maxYear: 2030,
        autoUpdateInput: false,
        locale: {
            format: 'DD.MM.YYYY'
        }
    });
    $('.daterange-empty').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
    });

    $('.daterange-empty').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
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
