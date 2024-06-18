<div id="advanced-search" class="collapse {{request()->has('client_id') ? 'show' : ''}}">
    <div class="px-3 d-flex justify-content-end align-items-start mb-3">
        <form action="{{ route('tenant.classes.guests.index') }}">
            <div style="width: 680px;" class="p-3 border-1 border-alpha-grey">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Client ID</label>
                            <input type="text" name="client_id" class="form-control form-control-sm" placeholder="Client ID" value="{{request('client_id')}}" />
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
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Guest name</label>
                            <input type="text" name="guest_name" class="form-control form-control-sm" placeholder="Guest name" value="{{request('guest_name')}}" />
                        </div>
                        <div class="form-group mb-0">
                            <label>Email address</label>
                            <input type="text" name="email" class="form-control form-control-sm" placeholder="Email address" value="{{request('email')}}" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label>&nbsp;</label>
                            <a href="{{ route('tenant.classes.guests.index') }}" title="" class="btn bg-grey btn-sm d-block">Reset search</a>
                        </div>
                    </div>
                    <div class="col-sm-6">
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
