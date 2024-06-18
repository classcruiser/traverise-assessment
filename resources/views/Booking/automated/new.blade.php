@extends('Booking.app') 

@section('content')

    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="/dashboard" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <span class="breadcrumb-item"><a href="{{route('tenant.automated-emails')}}" title="" class="text-grey">Automated Emails</a></span>
                <span class="breadcrumb-item active">New Automated Email</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h4 class="card-title">New Automated Email</h4>
                        <div class="header-elements">
                            <a href="{{route('tenant.automated-emails')}}" title="" class="btn btn-link text-danger">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>
                    <form action="{{route('tenant.automated-emails.insert')}}" method="post">
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
                                                <input type="text" name="name" placeholder="Name" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Subject</label>
                                                <input type="text" name="subject" placeholder="Subject" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label>Time value</label>
                                                <input type="number" name="send_time" min="1" placeholder="1" class="form-control" value="1">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Time unit</label>
                                                <select class="form-control select-no-search" data-fouc data-placeholder="Time unit" name="time_unit">
                                                    <option value="minutes">Minutes</option>
                                                    <option value="hours">Hours</option>
                                                    <option value="days">Days</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Timing</label>
                                                <select class="form-control select-no-search" data-fouc data-placeholder="Send timing" name="send_timing">
                                                    <option value="BEFORE">BEFORE</option>
                                                    <option value="AFTER">AFTER</option>
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
                                                                <option value="{{$column}}">&raquo; {{$column_label}}</option>
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
                                                <textarea name="resource" class="form-control frl" rows="20"></textarea>
                                            </div>
                                            <div class="notice-box text-sm p-2 mb-2">
                                                <p>You can use below code in your subject and template:</p>
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
                                                    <optgroup label="Bookings">
                                                        <option value="check_in">&raquo; Check In</option>
                                                        <option value="check_out">&raquo; Check Out</option>
                                                    </optgroup>
                                                    <optgroup label="Payment">
                                                        <option value="open_balance">&raquo; Open Balance</option>
                                                        <option value="payment_record">&raquo; Payment record</option>
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Operator</label>
                                                <select class="form-control select-no-search" data-fouc data-placeholder="Select operator" name="condition_operator">
                                                    <option></option>
                                                    <option value="is">&raquo; Is</option>
                                                    <option value="is_not">&raquo; Is not</option>
                                                    <option value="is_empty">&raquo; Is empty</option>
                                                    <option value="lt">&raquo; Less than</option>
                                                    <option value="lte">&raquo; Less than or equal</option>
                                                    <option value="gt">&raquo; Greater than</option>
                                                    <option value="gte">&raquo; Greater than or equal</option>
                                                    <option value="contains">&raquo; Contains</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Value</label>
                                                <input type="text" name="condition_value" placeholder="" class="form-control">
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
                                                    <a href="#" title="" class="text-danger toggle-camps">Toggle all camps</a>
                                                </div>

                                                <div class="border-1 border-alpha-grey">
                                                    @foreach($addons as $addon)
                                                        <div class="py-2 px-3 border-bottom-1 border-alpha-grey">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input checkbox-addon checkbox-{{$addon->id}}" id="addon-{{$addon->id}}" name="addons[{{$addon->id}}]">
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
                                                <div class="d-flex justify-content-between mb-2">
                                                    <label>Room category</label>
                                                    <a href="#" title="" class="text-danger toggle-camps">Toggle all camps</a>
                                                </div>

                                                <div class="border-1 border-alpha-grey">
                                                    @foreach($locations as $location)
                                                        <div class="py-2 px-3 alpha-grey {{$loop->last ? '' : 'border-bottom-1 border-alpha-grey'}}"><a href="javascript:" onClick="$('.checkbox-{{$location->id}}').attr('checked', true)" class="text-danger"><i class="fa fa-fw fa-home mr-1"></i> <b>{{$location->name}}</b></a></div>
                                                        @foreach($location->rooms as $room)
                                                            <div class=" py-2 px-3 border-bottom-1 border-alpha-grey">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" class="custom-control-input checkbox-camp checkbox-{{$location->id}}" id="room-{{$room->id}}" name="rooms[{{$room->id}}]">
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
                                                                <input type="checkbox" class="custom-control-input" id="document-{{$document->id}}" name="documents[{{$document->id}}]">
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

                        <div class="card-body">
                            <div class="text-right">
                                @csrf
                                <button class="btn bg-danger" type="submit">Save Template</button>
                            </div>
                        </div>
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
    $(document).ready(function() {
        $('textarea.frl').froalaEditor({
            charCounterCount: false,
            toolbarButtons: ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'insertLink', 'insertHR', 'insertTable', 'html'],
            tableStyles: {
                'payment-email': 'payment-email'
            },
            heightMin: 400,
            heightMax: 800
        });
        $('textarea.frl-sm').froalaEditor({
            charCounterCount: false,
            toolbarButtons: ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'insertLink', 'insertHR', 'insertTable', 'html'],
            tableStyles: {
                'payment-email': 'payment-email'
            },
            heightMin: 200,
            heightMax: 800
        });
        $('.toggle-camps').on('click', function(e) {
            e.preventDefault();
            const val = $('.checkbox-camp').attr('checked');
            $('.checkbox-camp').attr('checked', !val);
        })
    });
    </script>
@endsection