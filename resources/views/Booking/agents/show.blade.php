@extends('Booking.app') 

@section('content')

    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <span class="breadcrumb-item"><a href="{{route('tenant.agents')}}" title="" class="text-grey">Agents</a></span>
                <span class="breadcrumb-item active">{{$agent->name}}</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="page-content">
        <div class="content-wrapper container">
            <div class="content">
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h4 class="card-title">{{$agent->name}}</h4>
                        <div class="header-elements">
                            <a href="{{route('tenant.agents')}}" title="" class="btn btn-link text-danger">
                                <i class="far fa-angle-left mr-1"></i> Return
                            </a>
                        </div>
                    </div>
                    <form action="{{route('tenant.agents.update', [ 'id' => $id ])}}" method="post">
                        <div class="card-body border-top-1 border-alpha-grey pt-3">
                            @include('Booking.partials.form-messages') 
                            <div class="row">
                                <div class="col-sm-4">
                                    <h6>Details</h6>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>* Name</label>
                                                <input type="text" name="name" placeholder="Name" class="form-control" value="{{$agent->name}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>* Email</label>
                                                <input type="text" name="email" placeholder="Email" class="form-control" value="{{$agent->email}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Password</label>
                                                <input type="password" name="password" class="form-control" value="" placeholder="Leave blank to keep password">
                                                <span class="form-text text-muted">Leave blank to keep password</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>* Commission</label>
                                                <div class="input-group" style="width: 100px">
                                                    <input type="text" name="commission_value" class="form-control" value="{{$agent->commission_value}}" />
                                                    <span class="input-group-append">
                                                        <span class="input-group-text"><i class="far fa-percent"></i></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Active</label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="active" class="custom-control-input" id="form-active" {{$agent->is_active ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="form-active">Is Agent active?</label>
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
                                    <h6>Tax</h6>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Include tax calculation for:</label>
                                                <div class="py-1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" id="hotel-tax" class="custom-control-input" name="hotel_tax" @checked(boolVal($agent->tax['hotel_tax']) !== false)>
                                                        <label class="custom-control-label" for="hotel-tax">Hotel Tax</label>
                                                    </div>
                                                </div>
                                                <div class="py-1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" id="goods-tax" class="custom-control-input" name="goods_tax" @checked(boolVal($agent->tax['goods_tax']) !== false)>
                                                        <label class="custom-control-label" for="goods-tax">Goods Tax</label>
                                                    </div>
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
                                    <h6>Camp permissions</h6>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>* Camp Permission</label>
                                                <div class="border-1 border-alpha-grey">
                                                    @foreach($locations as $location)
                                                        <div class="py-1">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" id="location-{{$location->id}}" class="custom-control-input checkbox-{{$location->id}}" name="allowed_camps[{{$location->id}}]" {{$agent->allowed_camps_decoded->search($location->id) !== false ? 'checked' : ''}}>
                                                                <label class="custom-control-label" for="location-{{$location->id}}">{{$location->name}}</label>
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
                                                        <div class="py-2 px-3 alpha-grey {{$loop->last ? '' : 'border-bottom-1 border-alpha-grey'}}"><a href="javascript:" class="text-danger"><i class="fa fa-fw fa-home mr-1"></i> <b>{{$location->name}}</b></a></div>
                                                        @foreach($location->rooms as $room)
                                                            <div class="py-2 px-3 border-bottom-1 border-alpha-grey">
                                                                <label><i class="far fa-bed mr-1"></i> <b>{{$room->name}}</b></label>
                                                                @foreach($room->rooms as $r)
                                                                    <div class="py-1">
                                                                        <div class="custom-control custom-checkbox">
                                                                            <input type="checkbox" class="custom-control-input checkbox-{{$location->id}}" id="room-{{$r->id}}" name="rooms[{{$r->id}}]" {{$agent->rooms->contains('room_id', $r->id) ? 'checked' : ''}}>
                                                                            <label class="custom-control-label" for="room-{{$r->id}}">{{$r->name}}</label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
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

                        @can('manage agent')
                            <div class="card-body">
                                <div class="text-right">
                                    @csrf
                                    <button class="btn bg-danger" type="submit">Update Agent</button>
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
    </script>
@endsection