<div class="section-title bg-custom-primary text-uppercase px-3 rounded-1 font-size-lg">2 - Your Details</div>
@if($step == 2)
    <div class="section-body {{$step == 2 ? 'active' : ''}}">
        @if($errors->any())
            <div class="alert bg-kima text-white alert-dismissible">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <p class="mb-0">Please check the errors below</p>
            </div>
        @endif
        <form action="/book-package/{{$package->slug}}/details" method="post">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>* First Name</label>
                                <input type="text" name="fname" placeholder="First name" class="form-control {{$errors->has('fname') ? 'border-danger' : ''}}" value="{{old('fname')}}">
                                {!! $errors->has('fname') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>* Last Name</label>
                                <input type="text" name="lname" placeholder="Last name" class="form-control {{$errors->has('lname') ? 'border-danger' : ''}}" value="{{old('lname')}}">
                                {!! $errors->has('lname') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Company</label>
                                <input type="text" name="company" placeholder="Company" class="form-control" value="{{old('company')}}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>* Email</label>
                                <input type="text" name="email" placeholder="Email" class="form-control {{$errors->has('email') ? 'border-danger' : ''}}" value="{{old('email')}}">
                                {!! $errors->has('email') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>* Phone</label>
                                <input type="text" name="phone" placeholder="number only" class="form-control intTel {{$errors->has('phone') ? 'border-danger' : ''}}" value="{{old('phone')}}">
                                {!! $errors->has('phone') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>* Birthdate</label>
                                <div class="d-flex justify-content-between align-items-center">
                                    <input type="text" name="birthdate_day" placeholder="Day" class="form-control mr-3 {{$errors->has('birthdate_day') ? 'border-danger' : ''}}" style="width:60px" value="{{old('birthdate_day')}}">
                                    <select class="form-control {{$errors->has('birthdate_month') ? 'border-danger' : ''}}" name="birthdate_month">
                                        <option value="">Month</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                    <input type="text" name="birthdate_year" placeholder="Year" class="ml-3 form-control {{$errors->has('birthdate_year') ? 'border-danger' : ''}}" style="width:70px" value="{{old('birthdate_year')}}">
                                </div>
                                {!! $errors->has('birthdate_day') || $errors->has('birthdate_month') || $errors->has('birthdate_year') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>* Street</label>
                                <input type="text" name="street" placeholder="Street" class="form-control {{$errors->has('street') ? 'border-danger' : ''}}" value="{{old('street')}}">
                                {!! $errors->has('street') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>* City</label>
                                <input type="text" name="city" placeholder="City" class="form-control {{$errors->has('city') ? 'border-danger' : ''}}" value="{{old('city')}}">
                                {!! $errors->has('city') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>* Zip / Postal Code</label>
                                <input type="text" name="zip" placeholder="Zip / Postal Code" class="form-control {{$errors->has('zip') ? 'border-danger' : ''}}" value="{{old('zip')}}">
                                {!! $errors->has('zip') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>* Country</label>
                                <select class="form-control {{$errors->has('country') ? 'border-danger' : ''}}" name="country">
                                    <option value="">Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->country_name}}" {{ old('country') == $country->country_name ? 'selected' : '' }}>{{$country->country_name}}</option>
                                    @endforeach
                                </select>
                                {!! $errors->has('country') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="my-2">
                                @foreach($terms as $term)
                                    <label class="form-check">
                                        <input type="checkbox" name="terms[{{$term->id}}]" value="{{$term->id}}" class="mr-1 {{$errors->has('terms.'. $term->id .'') ? 'form-check-input-styled-danger' : 'form-check-input-styled'}}" data-fouc {{old('terms.'. $term->id) ? 'checked' : ''}} />
                                        I have read and agree to the <a href="/doc/{{$term->slug}}" data-popup title="{{$term->title}}" class="link-custom"><b>{{$term->title}}</b></a>
                                        {!! $errors->has('terms.'. $term->id .'') ? '<small class="font-sm text-danger">REQUIRED</small>' : '' !!}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(count($sp_transfers) > 0)
                <div class="border-top-1 border-alpha-grey pt-2 mt-2">
                    <h3>Transfers</h3>
                    <label class="form-check mb-3">
                        <input type="checkbox" name="skip_transfer" class="form-check-input-styled mr-1" data-toggle="collapse" data-target="#transfer-details" data-fouc {{old('skip_transfer') ? 'checked' : ''}} />
                        I will send flight details later
                    </label>
                    <div class="collapse {{old('skip_transfer') ? '' : 'show'}}" id="transfer-details">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>* Arrival date</label>
                                    <input type="text" name="arrival_date" class="form-control" value="{{session('sp_check_in')}}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>* Arrival time</label>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <select name="arrival_time_h" class="form-control mr-1 {{$errors->has('arrival_time_h') ? 'border-danger' : ''}}">
                                            <option value="">HH</option>
                                            @for($i = 1; $i <= 24; $i++)
                                                <option value="{{str_pad($i, 2, 0, STR_PAD_LEFT)}}">{{str_pad($i, 2, 0, STR_PAD_LEFT)}}</option>
                                            @endfor
                                        </select>
                                        <select name="arrival_time_m" class="form-control ml-1 {{$errors->has('arrival_time_m') ? 'border-danger' : ''}}">
                                            <option value="">MM</option>
                                            @for($i = 0; $i <= 60; $i++)
                                                <option value="{{str_pad($i, 2, 0, STR_PAD_LEFT)}}">{{str_pad($i, 2, 0, STR_PAD_LEFT)}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    {!! $errors->has('arrival_time_h') || $errors->has('arrival_time_m') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>* Arrival flight</label>
                                    <input type="text" name="arrival_flight" class="form-control {{$errors->has('arrival_flight') ? 'border-danger' : ''}}" value="{{old('arrival_flight')}}">
                                    {!! $errors->has('arrival_flight') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mb-2">
                                    <label>* Departure date</label>
                                    <input type="text" name="arrival_date" class="form-control" value="{{session('sp_check_out')}}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mb-2">
                                    <label>* Departure time</label>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <select name="departure_time_h" class="form-control mr-1 {{$errors->has('departure_time_h') ? 'border-danger' : ''}}">
                                            <option value="">HH</option>
                                            @for($i = 1; $i <= 24; $i++)
                                                <option value="{{str_pad($i, 2, 0, STR_PAD_LEFT)}}">{{str_pad($i, 2, 0, STR_PAD_LEFT)}}</option>
                                            @endfor
                                        </select>
                                        <select name="departure_time_m" class="form-control ml-1 {{$errors->has('departure_time_m') ? 'border-danger' : ''}}">
                                            <option value="">MM</option>
                                            @for($i = 0; $i <= 60; $i++)
                                                <option value="{{str_pad($i, 2, 0, STR_PAD_LEFT)}}">{{str_pad($i, 2, 0, STR_PAD_LEFT)}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    {!! $errors->has('departure_time_h') || $errors->has('departure_time_m') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mb-2">
                                    <label>* Departure flight</label>
                                    <input type="text" name="departure_flight" class="form-control {{$errors->has('departure_flight') ? 'border-danger' : ''}}" value="{{old('departure_flight')}}">
                                    {!! $errors->has('departure_flight') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="d-block">* WhatsApp Number</label>
                                    <input type="text" name="phone" class="form-control whatsapp {{$errors->has('phone') ? 'border-danger' : ''}}" value="{{old('phone')}}">
                                    {!! $errors->has('phone') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <input type="hidden" name="skip_transfer" value="on" />
            @endif
            @if($guest > 1)
                <div class="border-top-1 border-alpha-grey pt-2 mt-2">
                    <h3>Additional guest</h3>
                    <p>Please enter your group details below</p>

                    @for($i = 0; $i < (intVal($guest) - 1); $i++)
                        <div class="border-top-1 border-alpha-grey pt-2 mt-2">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>* Title</label>
                                        <select class="form-control " data-placeholder="Honorific" name="guest[{{$i}}][title]">
                                            <option value="Mr">Mr</option>
                                            <option value="Mrs">Mrs</option>
                                            <option value="Ms">Ms</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>* First Name</label>
                                        <input type="text" name="guest[{{$i}}][fname]" placeholder="First name" class="form-control {{$errors->has('guest.*.fname') ? 'border-danger' : ''}}" value="{{old('guest.'. $i .'.fname')}}">
                                        {!! $errors->has('guest.*.fname') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>* Last Name</label>
                                        <input type="text" name="guest[{{$i}}][lname]" placeholder="Last name" class="form-control {{$errors->has('guest.*.lname') ? 'border-danger' : ''}}" value="{{old('guest.'. $i .'.lname')}}">
                                        {!! $errors->has('guest.*.lname') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>* Email</label>
                                        <input type="text" name="guest[{{$i}}][email]" placeholder="Email" class="form-control {{$errors->has('guest.*.email') ? 'border-danger' : ''}}" value="{{old('guest.'. $i .'.email')}}">
                                        {!! $errors->has('guest.*.email') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>* Birthdate</label>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <input type="text" name="guest[{{$i}}][birthdate_day]" placeholder="Day" class="form-control mr-3 {{$errors->has('guest.*.birthdate_day') ? 'border-danger' : ''}}" style="width:60px" value="{{old('guest.'. $i .'.birthdate_day')}}">
                                            <select class="form-control {{$errors->has('guest.*.birthdate_month') ? 'border-danger' : ''}}" name="guest[{{$i}}][birthdate_month]">
                                                <option value="">Month</option>
                                                <option value="01">January</option>
                                                <option value="02">February</option>
                                                <option value="03">March</option>
                                                <option value="04">April</option>
                                                <option value="05">May</option>
                                                <option value="06">June</option>
                                                <option value="07">July</option>
                                                <option value="08">August</option>
                                                <option value="09">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                            <input type="text" name="guest[{{$i}}][birthdate_year]" placeholder="Year" class="ml-3 form-control {{$errors->has('guest.*.birthdate_year') ? 'border-danger' : ''}}" style="width:70px" value="{{old('guest.'. $i .'.birthdate_year')}}">
                                        </div>
                                        {!! $errors->has('guest.*.birthdate_day') || $errors->has('guest.*.birthdate_month') || $errors->has('guest.*.birthdate_year') ? '<span class="form-text text-danger">Required</span>' : '' !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            @endif
            <div class="d-flex justify-content-between mt-3">
                @csrf
                <a class="btn bg-grey text-uppercase font-size-lg btn-lg" href="/book-package/{{$slug}}">BACK</a>
                <button class="btn btn-custom text-uppercase font-size-lg btn-lg" type="submit">NEXT STEP</button>
            </div>
        </form>
    </div>
@endif

@section('scripts')
    @if($step == 1)
        <script>
        $('.daterange-single').daterangepicker({
            singleDatePicker: true,
            minDate: '{{$package->check_in->format('d M Y')}}',
            maxDate: '{{$package->check_out->format('d M Y')}}',
            autoApply: true,
            locale: {
                cancelLabel: 'Clear',
                format: 'DD MMM YYYY'
            }
        });
        var input = document.querySelector(".intTel");
        var input2 = document.querySelector(".whatsapp");
        window.intlTelInput(input, {
            utilsScript: '/js/intelinput/utils.js',
        });
        window.intlTelInput(input2, {
            utilsScript: '/js/intelinput/utils.js',
        });
        </script>
    @endif
@endsection
