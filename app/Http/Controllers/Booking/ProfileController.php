<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking\Profile;
use App\Services\Booking\FileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $profile = Profile::where('tenant_id', tenant('id'))->firstOrFail();

        $logo = file_exists(public_path('tenancy/assets/images/camps/'. tenant('id') .'_logo.jpg'));

        return view('Booking.profile.index', [
            'profile' => $profile,
            'logo' => $logo
        ]);
    }

    public function update()
    {
        Profile::where('tenant_id', tenant('id'))
            ->first()
            ->update(request()->only([
                'owner_name',
                'owner_phone',
                'owner_email',
                'ceo_name',
                'ceo_phone',
                'ceo_email',
                'contact_person',
                'contact_email',
                'phone_number',
                'vat_id',
                'stripe_id',
                'commercial_register_number',
                'district_court',
                'stripe_fee_percentage',
                'stripe_fee_fixed',
                'test_mode',
                'copy_email',
                'iban',
                'unpaid_booking_deletion_in',
                'google_ecomm_tag'
            ]));

        if (request()->has('logo')) {
            $response = (new FileService())->upload(request('logo'), '/tenancy/assets/images/camps', tenant('id') .'_logo.jpg', ['w' => 300, 'h' => 300]);
        }

        session()->flash('messages', 'Profile updated!');

        return redirect()->route('tenant.profile');
    }
}
