@extends('app')

@section('content')
<x-app>
    <div class="px-8 py-8 w-full mx-auto max-w-8xl">

        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-5">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Create new tenant</h1>
            </div>

            <a href="{{ route('tenants') }}" class="btn bg-gray-500 hover:bg-gray-600 text-white">
                <i class="fal fa-arrow-left fa-fw"></i>
                <span class="ml-2">Return</span>
            </a>

        </div>

        <x-alert-error />

        <form action="{{ route('tenants.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6 mt-8">
                    <div class="md:col-span-1">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Tenant details</h3>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="shadow sm:rounded-md sm:overflow-hidden">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid grid-cols-3 gap-6">
                                    <div class="col-span-3 sm:col-span-2">
                                        <label for="company-website" class="label">
                                            Identifier
                                        </label>
                                        <div class="flex items-center">
                                            <div class="mt-1 flex rounded-md shadow-sm w-full">
                                                <span
                                                    class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                    https://
                                                </span>
                                                <input type="text" name="id"
                                                    class="flex-1 focus:ring-indigo-300 focus:border-indigo-300 block w-48 rounded-none rounded-r-md sm:text-sm border-gray-300 rounded-r-none"
                                                    placeholder="domain" autocomplete="identifier" required id="domain"
                                                    value={{old('id')}}>
                                                <span
                                                    class="inline-flex items-center px-3 rounded-r-md border border-gray-300 bg-gray-50 text-gray-500 text-sm border-l-0 ">
                                                    {{ env('APP_URI') }}
                                                </span>
                                            </div>
                                            <a href="javascript:" title=""
                                                class="font-medium text-light-blue-500 ml-2 uppercase text-xs"
                                                id="domain-check"
                                                data-url="{{ route('tenants.domain-checker') }}">check</a>
                                        </div>
                                        <div class="text-xs uppercase mt-2" id="domain-check-placeholder"></div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="super_email" class="label">Super admin email</label>
                                        <input type="text" name="super_email" id="super_email" class="input" required
                                            value={{old('super_email')}}>
                                        <div class="mt-1 text-xs">This email will be used as the super admin account
                                        </div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="password" class="label">Password</label>
                                        <input type="password" name="password" id="password" class="input" required>
                                    </div>
                                </div>

                                <div>
                                    <label for="about" class="label">
                                        Tenant active status
                                    </label>
                                    <div class="mt-1">
                                        <div class="flex items-center"
                                            x-data="{ is_active: {{ old('is_active') === 'on' ? 'true' : 'false'}} }">
                                            <div class="form-switch">
                                                <input type="checkbox" id="switch-2" class="sr-only" name="is_active"
                                                    x-model="is_active" />
                                                <label class="bg-gray-400" for="switch-2">
                                                    <span class="bg-white shadow-sm" aria-hidden="true"></span>
                                                    <span class="sr-only">Switch label</span>
                                                </label>
                                            </div>
                                            <div class="text-sm text-gray-400 italic ml-2"
                                                x-text="is_active ? 'Active' : 'Inactive'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:grid md:grid-cols-3 md:gap-6 mt-8">
                    <div class="md:col-span-1">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Tenant information</h3>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="shadow sm:rounded-md sm:overflow-hidden px-4 py-5 bg-white space-y-6 sm:p-6">

                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="first_name" class="label">First name</label>
                                    <input type="text" name="first_name" id="first_name" autocomplete="given-name"
                                        class="input" value={{old('first_name')}}>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="last_name" class="label">Last name</label>
                                    <input type="text" name="last_name" id="last_name" autocomplete="family-name"
                                        class="input" value={{old('last_name')}}>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="email-address" class="label">Email address</label>
                                    <input type="text" name="email" id="email-address" autocomplete="email"
                                        class="input" value={{old('email')}}>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="country" class="label">Country</label>
                                    <select id="country" name="country" autocomplete="country-name" class="select">
                                        @foreach ($countries as $country)
                                        <option value="{{ $country->cc_iso2 }}" @selected($country->cc_iso2 == 'DE' ||
                                            old('country')==$country->cc_iso2)>
                                            {{ $country->country_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-6">
                                    <label for="address" class="label">Street address</label>
                                    <input type="text" name="address" id="address" autocomplete="address" class="input"
                                        value={{old('address')}}>
                                </div>

                                <div class="col-span-6 sm:col-span-6 lg:col-span-2">
                                    <label for="company" class="label">Company</label>
                                    <input type="text" name="company" id="company" autocomplete="company" class="input"
                                        value={{old('company')}}>
                                </div>

                                <div class="col-span-6 sm:col-span-6 lg:col-span-2">
                                    <label for="city" class="label">City</label>
                                    <input type="text" name="city" id="city" autocomplete="city" class="input"
                                        value={{old('city')}}>
                                </div>

                                <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                    <label for="state" class="label">State / Province</label>
                                    <input type="text" name="state" id="state" autocomplete="state" class="input"
                                        value={{old('state')}}>
                                </div>

                                <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                    <label for="zip" class="label">ZIP / Postal code</label>
                                    <input type="text" name="zip" id="zip" autocomplete="zip" class="input"
                                        value={{old('zip')}}>
                                </div>

                                <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                    <label for="phone" class="label">Phone number</label>
                                    <input type="text" name="phone" id="phone" autocomplete="phone" class="input"
                                        value={{old('phone')}}>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="text-right mt-4">
                    <input type="hidden" name="plan" value="premium" />
                    <input type="hidden" name="uid" value="{{ $uid }}" />
                    <button type="submit" name="submit"
                        class="btn bg-indigo-500 text-white font-medium py-2 px-3 rounded uppercase hover:bg-indigo-600 pointer-events-none opacity-75"
                        id="submit">submit</button>
                </div>
            </div>
        </form>

    </div>
</x-app>
@endsection