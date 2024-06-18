<div id="advanced-search" class="collapse {{request()->has('ref') ? 'show' : ''}}">
    <div class="px-3 d-flex justify-content-end align-items-start mb-3">
        <form action="{{route('tenant.bookings')}}" method="get">
            <div style="width: 680px;" class="p-3 border-1 border-alpha-grey">

                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Booking reference number</label>
                            <input type="text" name="ref" class="form-control form-control-sm" placeholder="Booking reference number" value="{{request('ref')}}" />
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Guest name</label>
                            <input type="text" name="guest_name" class="form-control form-control-sm" placeholder="Guest name" value="{{request('guest_name')}}" />
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="text" name="email" class="form-control form-control-sm" placeholder="Email address" value="{{request('email')}}" />
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Country</label>
                            <select class="form-control form-control-sm" name="country">
                                <option value="">All</option>
                                @foreach($countries as $country)
                                    <option value="{{$country->country_name}}" {{request('country') == $country->country_name ? 'selected' : ''}}>{{$country->country_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Stay dates</label>
                            <input type="text" name="stay_dates" class="form-control form-control-sm daterange-empty" placeholder="Stay dates" value="{{request('stay_dates')}}" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Payment method</label>
                            <select class="form-control form-control-sm" name="method">
                                <option value="">All</option>
                                <option value="paypal" {{request('method') == 'paypal' ? 'selected' : ''}}>Paypal</option>
                                <option value="stripe" {{request('method') == 'stripe' ? 'selected' : ''}}>Stripe</option>
                                <option value="banktransfer" {{request('method') == 'banktransfer' ? 'selected' : ''}}>Bank Transfer</option>
                                <option value="cash" {{request('method') == 'cash' ? 'selected' : ''}}>Cash</option>
                                <option value="transferwise" {{request('method') == 'transferwise' ? 'selected' : ''}}>Transferwise</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Location</label>
                            <select class="form-control multiselect" name="camps[]" multiple="multiple" data-fouc>
                                @foreach($locations as $camp)
                                    <option value="{{$camp->id}}" {{!request()->has('camps') || in_array($camp->id, request('camps')) ? 'selected' : ''}}>{{$camp->short_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Room</label>
                            <select class="form-control form-control-sm" name="room">
                                <option value="">All</option>
                                @foreach($locations as $location)
                                    <optgroup label="{{$location->name}}" disabled></optgroup>
                                    @foreach($location->rooms as $room)
                                        <option value="{{$room->id}}" {{request('room') == $room->id ? 'selected' : ''}}> &nbsp; {{$room->name}}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Added by</label>
                            <select class="form-control form-control-sm" name="user">
                                <option value="">All</option>
                                @foreach($users as $user)
                                    <option value="{{$user->id}}" {{request('user') == $user->id ? 'selected' : ''}}>{{$user->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Booking dates range</label>
                            <div class="d-flex justify-content-center align-items-center">
                                <input type="text" name="booking_date_from" class="form-control form-control-sm mr-2 date-basic" placeholder="From" value="{{request('booking_date_from')}}" autocomplete="off" />
                                -
                                <input type="text" name="booking_date_to" class="form-control form-control-sm ml-2 date-basic" placeholder="To" value="{{request('booking_date_to')}}" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Check in dates range</label>
                            <div class="d-flex justify-content-center align-items-center">
                                <input type="text" name="checkin_date_from" class="form-control form-control-sm mr-2 date-basic" placeholder="From" value="{{request('checkin_date_from')}}" autocomplete="off" />
                                -
                                <input type="text" name="checkin_date_to" class="form-control form-control-sm ml-2 date-basic" placeholder="To" value="{{request('checkin_date_to')}}" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Check out dates range</label>
                            <div class="d-flex justify-content-center align-items-center">
                                <input type="text" name="checkout_date_from" class="form-control form-control-sm mr-2 date-basic" placeholder="From" value="{{request('checkout_date_from')}}" autocomplete="off" />
                                -
                                <input type="text" name="checkout_date_to" class="form-control form-control-sm ml-2 date-basic" placeholder="To" value="{{request('checkout_date_to')}}" autocomplete="off" />
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Channel</label>
                            <select class="form-control form-control-sm" name="channel">
                                <option value="">All</option>
                                <option value="Dashboard" {{request('channel') == 'Dashboard' ? 'selected' : ''}}>Dashboard</option>
                                <option value="Online" {{request('channel') == 'Online' ? 'selected' : ''}}>Online</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group mb-0">
                            <label>Opportunity</label>
                            <select class="form-control form-control-sm" name="opportunity">
                                <option value="">All</option>
                                <option value="Pending" {{request('opportunity') == 'Pending' ? 'selected' : ''}}>Pending</option>
                                <option value="Sale {{request('opportunity') == 'Sale' ? 'selected' : ''}}">Sale</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group mb-0">
                            <label>Booking Status</label>
                            <select class="form-control multiselect" name="status[]" multiple="multiple" data-fouc>
                                <option value="ABANDONED" {{!request()->has('status') || in_array('ABANDONED', request('status')) ? 'selected' : ''}}>Abandoned</option>
                                <option value="CANCELLED" {{!request()->has('status') || in_array('CANCELLED', request('status')) ? 'selected' : ''}}>Canceled</option>
                                <option value="CONFIRMED" {{!request()->has('status') || in_array('CONFIRMED', request('status')) ? 'selected' : ''}}>Confirmed</option>
                                <option value="PENDING" {{!request()->has('status') || in_array('PENDING', request('status')) ? 'selected' : ''}}>Pending</option>
                                <option value="DRAFT" {{!request()->has('status') || in_array('DRAFT', request('status')) ? 'selected' : ''}}>Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Payment Status</label>
                            <select class="form-control form-control-sm" name="payment_status">
                                <option value="">All</option>
                                <option value="DUE" {{request('payment_status') == 'DUE' ? 'selected' : ''}}>Due</option>
                                <option value="COMPLETED" {{request('payment_status') == 'COMPLETED' ? 'selected' : ''}}>Completed</option>
                                <option value="PARTIAL" {{request('payment_status') == 'PARTIAL' ? 'selected' : ''}}>Partial</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-8">
                        <div class="form-group">
                            <label>Has Add-on</label>
                            <select class="form-control multiselect" name="has_addons[]" multiple="multiple" data-fouc>
                                @forelse ($addons as $addon)
                                    <option value="{{ $addon->id }}" {{ request()->has('has_addons') && in_array($addon->id, request('has_addons')) ? 'selected' : '' }}>{{ $addon->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group mb-0">
                            <label>Voucher Code</label>
                            <input type="text" name="voucher_code" class="form-control form-control-sm" placeholder="Voucher code" value="{{request('voucher_code')}}" />
                        </div>
                    </div>

                    <div class="col-sm-8"></div>
                    
                    <div class="col-sm-4">
                        <div class="form-group mb-0">
                            <label>&nbsp;</label>
                            <a href="/bookings" title="" class="btn bg-grey btn-sm d-block">Reset search</a>
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

@section('subscripts')
    <script>
    $('.multiselect').multiselect({
        nonSelectedText: 'All',
        includeSelectAllOption: true,
    });
    </script>
@endsection