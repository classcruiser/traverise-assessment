<div id="advanced-search" class="collapse {{request()->has('ref') ? 'show' : ''}}">
    <div class="px-3 d-flex justify-content-end align-items-start mb-3">
        <form action="{{ route('tenant.classes.multi-pass.orders') }}">
            <div style="width: 680px;" class="p-3 border-1 border-alpha-grey">

                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Order reference number</label>
                            <input type="text" name="ref" class="form-control form-control-sm" placeholder="Order reference number" value="{{request('ref')}}" />
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <select class="form-control form-control-sm" name="country">
                                <option value="">All</option>
                                @foreach($countries as $country)
                                    <option value="{{$country->country_name}}" {{request('country') == $country->country_name ? 'selected' : ''}}>{{$country->country_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label>Payment method</label>
                            <select class="form-control form-control-sm" name="method">
                                <option value="">All</option>
                                <option value="paypal" {{request('method') == 'paypal' ? 'selected' : ''}}>Paypal</option>
                                <option value="stripe" {{request('method') == 'stripe' ? 'selected' : ''}}>Stripe</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Guest name</label>
                            <input type="text" name="guest_name" class="form-control form-control-sm" placeholder="Guest name" value="{{request('guest_name')}}" />
                        </div>
                        <div class="form-group">
                            <label>Location</label>
                            <select class="form-control form-control-sm" name="location">
                                <option value="">All</option>
                                @foreach($locations as $location)
                                    <option value="{{$location->id}}" {{request('location') == $location->id ? 'selected' : ''}}>{{$location->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label>Order Status</label>
                            <select class="form-control form-control-sm" name="status">
                                <option value="">All</option>
                                <option value="DUE" {{request('status') == 'DUE' ? 'selected' : ''}}>DUE</option>
                                <option value="COMPLETED" {{request('status') == 'COMPLETED' ? 'selected' : ''}}>COMPLETED</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="text" name="email" class="form-control form-control-sm" placeholder="Email address" value="{{request('email')}}" />
                        </div>
                        <div class="form-group">
                            <label>Multi Pass</label>
                            <select class="form-control form-control-sm" name="multipass">
                                <option value="">All</option>
                                @foreach($passes as $key => $value)
                                    <option value="{{ $key }}" @selected(request('multipass') == $key)>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label>Order dates range</label>
                            <div class="d-flex justify-content-center align-items-center">
                                <input type="text" name="order_date_from" class="form-control form-control-sm mr-2 date-basic" placeholder="From" value="{{request('order_date_from')}}" autocomplete="off" />
                                -
                                <input type="text" name="order_date_to" class="form-control form-control-sm ml-2 date-basic" placeholder="To" value="{{request('order_date_to')}}" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group mb-0">
                            <label>&nbsp;</label>
                            <a href="{{ route('tenant.classes.multi-pass.orders') }}" title="" class="btn bg-grey btn-sm d-block">Reset search</a>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group mb-0">
                            <label>&nbsp;</label>
                            {!! csrf_field() !!}
                            <button class="btn d-block w-100 bg-danger btn-sm">Search</button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
