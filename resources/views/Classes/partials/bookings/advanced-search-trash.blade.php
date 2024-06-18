<div id="advanced-search" class="collapse {{request()->has('ref') ? 'show' : ''}}">
    <div class="px-3 d-flex justify-content-end align-items-start mb-3">
        <form action="{{ route('tenant.classes.bookings.trash') }}">
            <div style="width: 680px;" class="p-3 border-1 border-alpha-grey">

                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Booking reference number</label>
                            <input type="text" name="ref" class="form-control form-control-sm" placeholder="Booking reference number" value="{{request('ref')}}" />
                        </div>
                        <div class="form-group mb-0">
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
                            <label>Booker name</label>
                            <input type="text" name="booker_name" class="form-control form-control-sm" placeholder="Booker name" value="{{request('booker_name')}}" />
                        </div>
                        <div class="form-group mb-0">
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
                            <label>Email address</label>
                            <input type="text" name="email" class="form-control form-control-sm" placeholder="Email address" value="{{request('email')}}" />
                        </div>
                        <div class="form-group mb-0">
                            <label>Session</label>
                            <select class="form-control form-control-sm" name="class_session">
                                <option value="">All</option>
                                @foreach($categories as $category)
                                    <optgroup label="{{$category->name}}" disabled></optgroup>
                                    @foreach($category->classes as $classes)
                                        <option value="{{$classes->id}}" {{request('class_session') == $classes->id ? 'selected' : ''}}> &nbsp; {{$classes->name}}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group mb-0">
                            <label>&nbsp;</label>
                            <a href="{{ route('tenant.classes.bookings.index') }}" title="" class="btn bg-grey btn-sm d-block">Reset search</a>
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
