@extends('Booking.app')

@section('content')
<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
    <div class="d-flex">
        <div class="breadcrumb">
            <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <span class="breadcrumb-item">Settings</span>
            <a href="{{route('tenant.addons')}}" title="" class="breadcrumb-item">Addons</a>
            <span class="breadcrumb-item active">Edit addon</span>
        </div>

        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
</div>

<div class="page-content">
    <div class="content-wrapper container">
        <div class="content">
            @if(session()->has('messages'))
                <div class="alert bg-green-400 text-white alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <i class="fa fa-check-circle mr-1"></i> {{session('messages')}}
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <h4 class="card-title">Edit Addon</h4>
                    <div class="header-elements">
                        <a href="{{route('tenant.addons')}}" title="" class="btn btn-link text-danger">
                            <i class="far fa-angle-left mr-1"></i> Return
                        </a>
                    </div>
                </div>

                <form action="{{route('tenant.addons.update', ['id' => $addon->id])}}" method="post" id="new-offer" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <h6>Details</h6>
                            </div>
                            <div class="col-sm-8">

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Name</label>
                                            <input type="text" name="name" placeholder="Name" class="form-control" value="{{$addon->name}}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Rate type</label>
                                            <select class="form-control select-no-search" data-fouc data-placeholder="Rate type" name="rate_type" required>
                                                <option></option>
                                                <option value="Day" {{$addon->rate_type == 'Day' ? 'selected' : ''}}>Day</option>
                                                <option value="Fixed" {{$addon->rate_type == 'Fixed' ? 'selected' : ''}}>Fixed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Base price</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-fw fa-euro-sign"></i></span>
                                                </span>
                                                <input type="text" name="base_price" class="form-control form-control-sm" placeholder="0.0" required value="{{$addon->base_price}}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>* Unit name</label>
                                            <input type="text" name="unit_name" placeholder="Guest, Car etc" class="form-control" value="{{$addon->unit_name}}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>* Description</label>
                                            <textarea name="description" class="form-control" rows="5" placeholder="Addon description">{{$addon->description}}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Min. days</label>
                                            <input type="number" name="min_stay" class="form-control form-control-sm" placeholder="1" value="{{$addon->min_stay}}" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Max. days</label>
                                            <input type="number" name="max_stay" class="form-control form-control-sm" placeholder="1" value="{{$addon->max_stay}}" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Min. Unit</label>
                                            <input type="number" name="min_units" class="form-control form-control-sm" placeholder="1" value="{{$addon->min_units}}" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Max. Unit</label>
                                            <input type="number" name="max_units" class="form-control form-control-sm" placeholder="Leave blank for no limit" value="{{$addon->max_units}}" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Qty</label>
                                            <input type="number" name="qty" class="form-control form-control-sm" placeholder="Leave blank for no limit" value="{{$addon->qty}}" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Sort</label>
                                            <input type="number" name="sort" class="form-control form-control-sm" placeholder="1" value="{{$addon->sort}}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="active" class="custom-control-input" id="form-active" {{$addon->active ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="form-active">Active</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="admin_only" class="custom-control-input" id="form-admin" {{$addon->admin_only ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="form-admin">Only visible in backend</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="add_default" class="custom-control-input" id="form-add_default" {{$addon->add_default ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="form-add_default">Added by default</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="week_question" class="custom-control-input" id="form-week_question" {{$addon->week_question ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="form-week_question">Show starting week question drop down</label>
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
                                <h6>Questionnaire</h6>
                            </div>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <select class="form-control" name="questionnaire_id"
                                                    data-placeholder="Questionnaire">
                                                <option value="" selected>No questionnaire</option>
                                                @foreach($questionnaires as $questionnaire)
                                                    <option
                                                        value="{{$questionnaire->id}}" {{$addon->questionnaire_id == $questionnaire->id ? 'selected' : ''}}>{{$questionnaire->name}}</option>
                                                @endforeach
                                            </select>
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
                                            <label>Room category</label>

                                            <div class="border-1 border-alpha-grey">
                                                @foreach($locations as $location)
                                                    <div class="py-2 px-3 alpha-grey {{$loop->last ? '' : 'border-bottom-1 border-alpha-grey'}}">
                                                        <a href="javascript:" onClick="$('.checkbox-{{$location->id}}').attr('checked', true)" class="text-danger"><i class="fa fa-fw fa-home mr-1"></i> <b>{{$location->name}}</b></a>
                                                    </div>
                                                    @foreach($location->rooms as $room)
                                                        <div class=" py-2 px-3 border-bottom-1 border-alpha-grey">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input checkbox-{{$location->id}}" id="room-{{$room->id}}" name="rooms[{{$room->id}}]" {{$addon->rooms->contains('room_id', $room->id) ? 'checked' : ''}}>
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
                        <h4>Addon picture</h4>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-4">Select picture</label>
                            <div class="col-sm-8">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" name="file">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                </div>

                                <p><b>CURRENT PICTURE</b></p>
                                @if(!$picture)
                                    <em>No picture uploaded yet</em>
                                @else
                                    <img src="{{$picture}}?{{time()}}" alt="" class="d-block" />
                                @endif
                            </div>
                        </div>
                    </div>

                    @can('edit addon')
                        <div class="card-body">
                            <div class="text-right">
                                @csrf
                                <input type="hidden" name="old_rooms" value="{{json_encode($addon->rooms)}}" />
                                <button class="btn bg-danger">Submit</button>
                            </div>
                        </div>
                    @endcan
                    <!-- end card body -->

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
    })
    $('.daterange-basic').daterangepicker({
        autoApply: false,
        autoUpdateInput: false,
        locale: {
            format: 'DD.MM.YYYY',
            cancelLabel: "Clear"
        }
    });
    $('.daterange-basic').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
    });
    $('#tax').on('click', function (e) {
        $('#tax-container').toggle();
    })
</script>
@endsection
