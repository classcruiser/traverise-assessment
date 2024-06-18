@extends('Booking.app')

@section('content')
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{route('tenant.dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <span class="breadcrumb-item">Settings</span>
                <span class="breadcrumb-item active">Profile</span>
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
                    <div class="card-header header-elements-inline">
                        <h4 class="card-title"><i class="fa fa-user-tie fa-fw"></i> Profile</h4>
                    </div>
                    <div class="card-body border-top-1 border-alpha-grey pt-3">
                        <form action="{{route('tenant.profile.update')}}" method="post" enctype="multipart/form-data">
                            <table class="w-full" style="width: 100%">
                                <tr>
                                    <td width="35%" class="text-base py-2" valign="top">Owner name</td>
                                    <td class="py-2" width="65%"><input type="text" name="owner_name" class="form-control" value="{{$profile->owner_name}}" /></td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Owner Email</td>
                                    <td class="py-2"><input type="text" name="owner_email" class="form-control" value="{{$profile->owner_email}}" /></td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Owner Phone Number</td>
                                    <td class="py-2"><input type="text" name="owner_phone" class="form-control" value="{{$profile->owner_phone}}" /></td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">CEO Name</td>
                                    <td class="py-2"><input type="text" name="ceo_name" class="form-control" value="{{$profile->ceo_name}}" /></td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">CEO Email</td>
                                    <td class="py-2"><input type="text" name="ceo_email" class="form-control" value="{{$profile->ceo_email}}" /></td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">CEO Phone Number</td>
                                    <td class="py-2"><input type="text" name="ceo_phone" class="form-control" value="{{$profile->ceo_phone}}" /></td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Contact Person</td>
                                    <td class="py-2"><input type="text" name="contact_person" class="form-control" value="{{$profile->contact_person}}" /></td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Contact Email</td>
                                    <td class="py-2">
                                        <input type="text" name="contact_email" class="form-control" value="{{$profile->contact_email}}" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Copy Email</td>
                                    <td class="py-2"><input type="text" name="copy_email" class="form-control" value="{{$profile->copy_email}}" placeholder="If specified, all emails except automated emails will be bcc'ed to this address" /></td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Contact Phone Number</td>
                                    <td class="py-2"><input type="text" name="phone_number" class="form-control" value="{{$profile->phone_number}}" /></td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">IBAN</td>
                                    <td class="py-2"><input type="text" name="iban" class="form-control" value="{{$profile->iban}}" /></td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">VAT ID</td>
                                    <td class="py-2"><input type="text" name="vat_id" class="form-control" value="{{$profile->vat_id}}" /></td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Stripe ID</td>
                                    <td class="py-2">
                                        <input type="text" name="stripe_id" class="form-control" value="{{$profile->stripe_id ?? tenant('stripe_account_id')}}" />
                                        <div class="bg-orange-50 p-2 mt-2">
                                            <span class="text-orange-700">
                                                <i class="fa fa-exclamation-triangle mr-1"></i>
                                                Changing Stripe Account ID will require you to go through the process of onboarding again.
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Commercial Register Number</td>
                                    <td class="py-2"><input type="text" name="commercial_register_number" class="form-control" value="{{$profile->commercial_register_number}}" /></td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">District Court</td>
                                    <td class="py-2"><input type="text" name="district_court" class="form-control" value="{{$profile->district_court}}" /></td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Stripe Fee (percentage)</td>
                                    <td class="py-2">
                                        <div class="flex justify-start align-items-center">
                                            <input type="text" name="stripe_fee_percentage" class="form-control" style="max-width: 70px;" value="{{$profile->stripe_fee_percentage}}" />
                                            <span class="ml-2 text-gray-500"><i class="far fa-circle-exclamation mr-1"></i> Use period as decimal, not comma. (e.g 2.50 instead of 2,50)</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Stripe Fee (fixed)</td>
                                    <td class="py-2">
                                        <div class="flex justify-start align-items-center">
                                            <input type="text" name="stripe_fee_fixed" class="form-control" style="max-width: 70px" value="{{$profile->stripe_fee_fixed}}" />
                                            <span class="ml-2 text-gray-500"><i class="far fa-circle-exclamation mr-1"></i> Use period as decimal, not comma. (e.g 2.50 instead of 2,50)</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Payment Mode</td>
                                    <td class="py-2">
                                        <select name="test_mode" class="form-control" style="max-width: 100px">
                                            <option value="0" {{!$profile->test_mode ? 'selected' : ''}}>LIVE</option>
                                            <option value="1" {{$profile->test_mode ? 'selected' : ''}}>TEST</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Enable Google E-commerce Tag</td>
                                    <td class="py-2">
                                        <select name="google_ecomm_tag" class="form-control" style="max-width: 100px">
                                            <option value="1" @checked($profile->google_ecomm_tag)>Enable</option>
                                            <option value="0" @checked(!$profile->google_ecomm_tag)>Disable</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Unpaid booking deletion time</td>
                                    <td class="py-2">
                                        <div class="flex justify-start align-items-center">
                                            <div class="input-group" style="max-width: 120px;">
                                                <input type="text" name="unpaid_booking_deletion_in" class="form-control" style="max-width: 50px;" value="{{$profile->unpaid_booking_deletion_in}}" />
                                                <span class="input-group-append">
                                                    <span class="input-group-text">HOUR</span>
                                                </span>
                                            </div>
                                            <span class="ml-2 text-gray-500"><i class="far fa-circle-exclamation mr-1"></i> Enter number when unpaid booking will be deleted after it is submitted.<br />Enter 0 to disable</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-base py-2" valign="top">Logo</td>
                                    <td class="py-2">
                                        <input type="file" name="logo" />
                                        <em class="mt-1 d-inline-block">Maximum 300px x 300px. format must be .JPG</em>
                                        @if($logo)
                                            <div class="mt-2">
                                                <b class="text-uppercase d-inline-block mb-2">Current logo</b><br />
                                                <img src="{{asset('images/camps/'. tenant('id') .'_logo.jpg')}}" alt="" />
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <div class="mt-4 flex justify-center gap-2">
                                @csrf
                                <button class="btn btn-danger uppercase font-bold" name="submit">save</button>
                            </div>
                        </form>
                    </div>
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
