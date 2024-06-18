@extends('Booking.app') 

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <a href="{{route('tenant.transfers')}}" title="" class="breadcrumb-item">Transfers</a>
                <span class="breadcrumb-item active">Add transfer</span>
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
                        <h4 class="card-title">Add Transfer</h4>
                        <div class="header-elements">
                            <a href="{{route('tenant.transfers')}}" title="" class="btn btn-link text-danger">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>

                    <form action="{{route('tenant.transfers.insert')}}" method="post" id="new-offer">
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
                                                <label>* Direction</label>
                                                <select class="form-control select-no-search" data-fouc data-placeholder="Rate type" name="direction" required>
                                                    <option></option>
                                                    <option value="Inbound">Inbound</option>
                                                    <option value="Outbound">Outbound</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>* Description</label>
                                                <textarea name="description" class="form-control" rows="5" placeholder="Transfer description"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="add_default" class="custom-control-input" id="form-active">
                                                    <label class="custom-control-label" for="form-active">Add default?</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="is_complimentary" class="custom-control-input" id="form-admin">
                                                    <label class="custom-control-label" for="form-admin">Complimentary?</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Min. nights (default)</label>
                                            <input type="number" name="default_min_nights" class="form-control form-control-sm" placeholder="Minimum nights" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Min. nights (complimentary)</label>
                                            <input type="number" name="complimentary_min_nights" class="form-control form-control-sm" placeholder="Minimum nights" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="is_active" class="custom-control-input" id="form-is_active" checked>
                                                    <label class="custom-control-label" for="form-is_active">Active</label>
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
                                    <h6>Pricing</h6>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">

                                        <div class="col-sm-12 collapse show">

                                            @for($i = 0; $i < 10; $i++)
                                                <div class="py-2 border-bottom-1 border-alpha-grey">
                                                    <div class="d-flex justify-content-start align-items-center">

                                                        <span class="mr-2">Guest</span>

                                                        <div style="width: 50px;" class="mr-2 ml-auto">
                                                            <input type="text" class="form-control" value="{{$i + 1}}" />
                                                        </div>

                                                        <div>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend">
                                                                    <span class="input-group-text">&euro;</span>
                                                                </span>
                                                                <input type="text" class="form-control" placeholder="Price" name="price[{{$i}}]" style="width: 70px" />
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            @endfor

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
                                                                    <input type="checkbox" class="custom-control-input checkbox-{{$location->id}}" id="room-{{$room->id}}" name="rooms[{{$room->id}}]">
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

                        @can('save setting')
                            <div class="card-body">
                                <div class="text-right">
                                    {!! csrf_field() !!}
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
    });
    </script>
@endsection