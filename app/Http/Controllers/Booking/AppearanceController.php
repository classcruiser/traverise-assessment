<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;

use App\Models\Booking\Profile;
use App\Models\Booking\Location;
use App\Services\Booking\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AppearanceController extends Controller
{
    public function index()
    {
        $customize = Profile::where('tenant_id', tenant('id'))->firstOrFail();

        return view('Booking.appearances.index', [
            'settings' => $customize,
            'preview' => false,
            'preview_data' => null,
            'location' => Location::latest()->first(),
        ]);
    }

    public function update()
    {
        $preview_data = [];
        $preview = false;

        $customize = Profile::where('tenant_id', tenant('id'))->firstOrFail();

        $destination = '/bucket/';
        $file_name = null;

        if (request()->has('file')) {
            if (request()->has('preview_file') && request('preview_file') != '') {
                @unlink(public_path($destination . request('preview_file')));
            }

            $file_name = tenant('id') . '.jpg';

            $response = (new FileService())->upload(
                request('file'),
                $destination,
                $file_name,
                ['w' => 1920, 'h' => 600]
            );
        }

        if (request()->has('preview')) {
            $preview_data = request()->except(['preview', '_token']);
            $preview_data['file'] = $file_name;
            $preview = true;
        }

        // if user press preview and still preview file from previous upload
        if (request()->has('preview') && request()->has('preview_file') && request('preview_file') != '') {
            //@unlink(public_path($destination . request('preview_file')));
        }

        if (request()->has('submit')) {
            $customize->update(request()->except(['preview', '_token', 'submit', 'preview_file', 'file']));

            if (request()->has('preview_file') && request('preview_file') != '' && !$file_name) {
                // from old preview
                @copy(base_path('/public/' . $destination . request('preview_file')), base_path('/public/tenancy/assets/front/' . tenant('id') . '_header.jpg'));
            }

            if ($file_name) {
                @copy(base_path('/public/' . $destination . $file_name), base_path('/public/tenancy/assets/front/' . tenant('id') . '_header.jpg'));
            }

            session()->flash('messages', 'Settings saved');

            return redirect()->route('tenant.appearances', [time()]);
        }

        return view('Booking.appearances.index', [
            'settings' => $customize,
            'preview' => $preview,
            'preview_data' => $preview_data,
            'preview_url' => $file_name,
            'location' => Location::latest()->first(),
        ]);
    }
}
