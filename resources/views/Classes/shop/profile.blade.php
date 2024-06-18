@extends('Classes.app')

@section('content')
    @include('Classes.shop.popup')
    <div class="w-screen min-h-screen bg-gray-50 relative">
        @include('Classes.shop.drawer')

        <x-shop.container>
            <x-shop.header/>
            <x-shop.heading step="3">
                <div class="py-8 px-6">
                    @if (session()->has('error'))
                        <div
                            class="mb-6 p-4 alert bg-danger-400 text-white alert-dismissible border has-error text-sm leading-relaxed">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h3 class="mb-4 text-lg font-bold">Booker details</h3>
                    @if(!$guest)
                        <form action="{{ route('guest.login.attempt') }}" method="post">
                            @csrf
                            <div class="mt-6 mb-6">
                                <label class="block mt-2">
                                    <input type="checkbox" name="login_toggle" class="mr-1" value="1"
                                           id="login_toggle" @checked(old('login_toggle')) />
                                    <span class="text-lg underline">Bitte logge dich ein, falls du deinen Multipass nutzen möchtest!</span>
                                </label>
                            </div>

                            <div class="{{ old('login_toggle') ? '' : 'hidden' }}" id="login_form">
                                <div class="grid grid-cols-12 gap-6">
                                    <div class="col-span-6">
                                        <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                                            Email *
                                        </label>
                                        <input type="email" name="email"
                                               class="@error('email') has-error @enderror w-full border border-gray-200 py-2 px-3 text-sm rounded"
                                               required/>
                                    </div>
                                    <div class="col-span-6">
                                        <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                                            Password
                                        </label>
                                        <input class="w-full border  border-gray-200 py-2 px-3 text-sm rounded"
                                               name="password"
                                               type="password"
                                               required/>
                                    </div>
                                </div>

                                <div class="mt-4 flex justify-end items-center">
                                    @if (!auth('guest')->check())
                                        <a href="{{ route('guest.forgot-password') }}" title="" class="block mr-4" target="_blank">forgot password</a>
                                    @endif
                                    <x-shop.button type="button" style="primary">
                                        LOGIN <i class="ml-1 fal fa-arrow-right"></i>
                                    </x-shop.button>
                                </div>
                            </div>
                        </form>
                    @endif

                    <form action="{{route('class.shop.save-profile')}}" method="post">
                        @csrf

                        @if(!$guest)
                            <div class="grid grid-cols-12 gap-6" id="guest_form">
                                <div class="col-span-6">
                                    <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                                        First Name *
                                    </label>
                                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                                           class="w-full border @error('first_name') has-error @enderror border-gray-200 py-2 px-3 text-sm rounded"
                                           required/>
                                    @error('first_name')
                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-span-6">
                                    <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                                        Last Name
                                    </label>
                                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                                           class="w-full border @error('last_name') has-error @enderror border-gray-200 py-2 px-3 text-sm rounded"
                                           required/>
                                    @error('last_name')
                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-span-6">
                                    <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                                        Email
                                    </label>
                                    <input type="text" name="email" value="{{ old('email') }}"
                                           class="w-full border @error('email') has-error @enderror border-gray-200 py-2 px-3 text-sm rounded"
                                           required/>
                                    @error('email')
                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-span-6">
                                    <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                                        Phone
                                    </label>
                                    <input type="text" name="phone" value="{{ old('phone') }}"
                                           class="w-full border @error('phone') has-error @enderror border-gray-200 py-2 px-3 text-sm rounded"
                                           placeholder="number only" required/>
                                    @error('phone')
                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-span-6">
                                    <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                                        Address
                                    </label>
                                    <input type="text" name="address" value="{{ old('address') }}"
                                           class="w-full border @error('address') has-error @enderror border-gray-200 py-2 px-3 text-sm rounded"
                                           required/>
                                    @error('address')
                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-span-6">
                                    <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                                        Country
                                    </label>
                                    <select name="country"
                                            class="w-full border border-gray-200 py-2 px-3 text-sm rounded"
                                            required>
                                        <option value="">Select country</option>
                                        @foreach ($countries as $country)
                                            <option
                                                value="{{ $country->cc_iso2 }}" {{ $country->cc_iso2 == old('country', 'DE') ? 'selected' : '' }}>{{ $country->country_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @else
                            <p class="mt-6 mb-2">
                                <span class="font-bold text-base">{{ $guest->fname.' '.$guest->lname }}</span>
                                <br>
                                {{ $guest->email }}
                                <br>
                            </p>
                            <p>
                                <span class="font-bold">Address</span>
                                <br>
                                {{ $guest->street }}
                            </p>
                        @endif

                        @if (!$booker_only)
                            <div class="flex justify-between items-center mt-8 mb-4 ">
                                <h3 class="text-lg font-bold">Guest details</h3>
                            </div>

                            <div id="guests_container">
                                @php
                                    $index = 0;
                                @endphp
                                @foreach ($session['sessions'] as $class)
                                    @for ($i = 0; $i < $class['quantity']; $i++)
                                        <div id="guest-{{ $index }}" class="guest-row mb-10">
                                            <h4 class="mt-6 mb-2 text-sm text-gray-700 font-bold">{{ $index + 1 }}
                                                . {{ $class['name'] }}</h4>
                                            <div class="grid grid-cols-12 gap-6">
                                                <div class="col-span-6">
                                                    <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                                                        First Name *
                                                    </label>
                                                    <input type="text" name="guest[{{ $index }}][first_name]"
                                                        value="{{ old('guest.'. $i .'.first_name')}}"
                                                        class="w-full border @error('guest.'. $i .'.first_name') has-error @enderror border-gray-200 py-2 px-3 text-sm rounded"
                                                        required/>
                                                    @error('guest.'. $i .'.first_name')
                                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-span-6">
                                                    <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                                                        Surname
                                                    </label>
                                                    <input type="text" name="guest[{{ $index }}][last_name]"
                                                        class="w-full border @error('guest.'. $i .'.last_name') has-error @enderror border-gray-200 py-2 px-3 text-sm rounded"
                                                        required/>
                                                    @error('guest.'. $i .'.last_name')
                                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-span-6">
                                                    <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                                                        Email
                                                    </label>
                                                    <input type="text" name="guest[{{ $index }}][email]"
                                                        class="w-full border @error('guest.'. $i .'.email') has-error @enderror border-gray-200 py-2 px-3 text-sm rounded"
                                                        required/>
                                                    @error('guest.'. $i .'.email')
                                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-span-3 md:col-span-2">
                                                    <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                                                        Weight *
                                                    </label>
                                                    <div class="flex justify-start items-center">
                                                        <input type="number" name="guest[{{ $index }}][weight]" min="32"
                                                            max="300"
                                                            class="w-full border @error('guest.'. $i .'.weight') has-error @enderror border-gray-200 py-2 px-3 text-sm rounded"
                                                            required/>
                                                        <span class="ml-2">Kg</span>
                                                    </div>
                                                    @error('guest.'. $i .'.weight')
                                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                    @enderror
                                                    <input type="hidden" name="guest[{{ $index }}][class_schedule_id]" value="{{ $class['id'] }}"/>
                                                    <input type="hidden" name="guest[{{ $index }}][class_session_id]" value="{{ $class['session_id'] }}"/>
                                                    <input type="hidden" name="guest[{{ $index }}][class_date]" value="{{ $class['date'] }}"/>
                                                    <input type="hidden" name="guest[{{ $index }}][class_name]" value="{{ $class['name'] }}"/>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $index++;
                                        @endphp
                                    @endfor
                                @endforeach
                            </div>
                        @else
                            <input type="hidden" name="booker_only" value="1" />
                        @endif

                        <div class="mt-8">
                            @foreach($terms as $term)
                                <label class="block">
                                    <input type="checkbox" name="terms[{{$term->id}}]" class="mr-1" @checked(old('terms['. $term->id .']')) required/>
                                    I have read and agree to the <a href="/doc/{{$term->slug}}" data-popup title="{{$term->title}}" class="link-custom"><b>{{$term->title}}</b></a>
                                    @error('terms')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </label>
                                @if(!$guest)
                                    <label class="block mt-2">
                                        <input type="checkbox" name="new_account" class="mr-1"
                                               @checked(old('new_account')) id="account-toggle"/>
                                        Confirm and create my account for faster checkout next time
                                    </label>
                                    <div class="hidden mt-4" id="account-container">
                                        <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                                            Create a password
                                        </label>
                                        <input type="password" name="new_password"
                                               class="w-1/3 border border-gray-200 py-2 px-3 text-sm rounded"
                                               autocomplete="new-password"/>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="mt-16 flex justify-between">
                            <x-shop.button href="/book-class/addons{{ request()->has('soft_op') ? '?soft_op='. request('soft_op') : '' }}" style="secondary">
                                <i class="ml-1 fal fa-arrow-left"></i> PREVIOUS
                            </x-shop.button>
                            <x-shop.button type="button" :btn="['type' => 'submit', 'name' => 'submit']" style="primary">
                                NEXT <i class="ml-1 fal fa-arrow-right"></i>
                            </x-shop.button>
                        </div>
                    </form>
                </div>
            </x-shop.heading>
        </x-shop.container>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
@endsection

@section('scripts')
    <script>
        let container = $('#guests_container');
        let maxLength = {{ $max_guests }};

        $(document).on('click', '.add-guest', function (e) {
            e.preventDefault();
            let guests = $('.guest-row').length;

            if (guests >= maxLength) {
                alert('You can\'t add more than ' + maxLength + ' guests');
                return;
            }

            let nextPosition = guests + 1;

            let html = `
            <div id="guest-${nextPosition}" class="guest-row">
                <div class="flex justify-start items-center mt-4 mb-2 ">
                    <h4 class="text-sm text-gray-700">Guest ${nextPosition}</h4>
                    <a href="javascript:" title="" class="remove-guest ml-2 text-xs text-rose-600" data-id="${nextPosition}">Remove <i class="fal fa-times ml-1"></i></a>
                </div>
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-6">
                        <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                            Full Name
                        </label>
                        <input type="text" name="guest[${nextPosition - 1}][name]" class="w-full border border-gray-200 py-2 px-3 text-sm rounded" required/>
                    </div>
                    <div class="col-span-6">
                        <label class="block tracking-wide text-xs uppercase text-gray-600 mb-2">
                            Birthdate
                        </label>
                        <input type="date" name="guest[${nextPosition - 1}][birthdate]" class="w-full border border-gray-200 py-2 px-3 text-sm rounded" required/>
                    </div>
                </div>
            </div>`;

            container.append(html);
        });
        
        if (document.querySelector('#login_toggle')) {
            document.querySelector('#login_toggle').addEventListener('click', function (e) {
                document.querySelector('#login_form').classList.toggle('hidden');
                document.querySelector('#guest_form').classList.toggle('hidden');
            });
        }
    </script>
@endsection
