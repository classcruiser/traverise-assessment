@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <a href="{{route('tenant.special-packages')}}" title="" class="breadcrumb-item">Special Packages</a>
                <span class="breadcrumb-item active">New special package</span>
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
                        <h4 class="card-title">New Special Package</h4>
                        <div class="header-elements">
                            <a href="{{route('tenant.special-packages')}}" title="" class="btn btn-link text-danger">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>

                    <form action="{{route('tenant.special-packages.insert')}}" method="post" id="edit-package">
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
                                                <input type="text" name="name" placeholder="Name" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>* Room</label>
                                                <select class="form-control select-no-search" data-fouc data-placeholder="Camp" name="room_id" required>
                                                    <option></option>
                                                    @foreach($locations as $location)
                                                        <optgroup label="{{$location->name}}">
                                                            @foreach($location->rooms as $room)
                                                                <option value="{{$room->id}}">{{$room->name}}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Price</label>
                                                <div class="input-group">
                                                    <span class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-fw fa-euro-sign"></i></span>
                                                    </span>
                                                    <input type="number" name="price" placeholder="Price" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Pickup</label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="airport_pickup" class="custom-control-input" id="airport-pickup">
                                                    <label class="custom-control-label" for="airport-pickup">Include ?</label>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Dropoff</label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="airport_dropoff" class="custom-control-input" id="airport-dropoff">
                                                    <label class="custom-control-label" for="airport-dropoff">Include ?</label>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="description" class="form-control" rows="4" placeholder="Description"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Package Inclusions</label>
                                                <textarea name="inclusions" class="form-control" rows="20" placeholder="Package inclusions"></textarea>
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
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Stay dates</label>
                                                <input type="text" name="dates" placeholder="Dates" class="form-control daterange-basic" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Total Nights</label>
                                                <input type="number" name="nights" placeholder="Total nights" class="form-control" value="1">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Minimum Guest</label>
                                                <input type="number" name="min_guest" placeholder="Minimum guest" class="form-control" value="1">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Maximum Guest</label>
                                                <input type="number" name="max_guest" placeholder="Maximum guest" class="form-control" value="">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <h6>Included Addons</h6>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Addons</label>

                                                <table class="table table-xs">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">INCLUDED</th>
                                                            <th class="text-center">HIDE</th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($extras as $addon)
                                                            <tr>
                                                                <td class="text-center"><input type="checkbox" class="" name="addons[{{$addon->id}}]"></td>
                                                                <td class="text-center"><input type="checkbox" class="" name="hide[{{$addon->id}}]"></td>
                                                                <td>{{$addon->name}}</td>
                                                                <td><input type="text" name="addons_qty[{{$addon->id}}]" class="form-control form-control-sm" value="{{$addons[$addon->id] ?? 0}}" style="width: 50px;" /></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>

                        @can('save setting')
                            <div class="card-body">
                                <div class="text-right">
                                    {!! csrf_field() !!}
                                    <button class="btn bg-danger" type="submit">Submit</button>
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
    </script>
@endsection
