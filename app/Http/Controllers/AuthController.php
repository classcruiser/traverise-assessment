<?php

namespace App\Http\Controllers;

use App\Http\Requests\PanelLoginRequest;
use App\Models\Booking\User as UserTenant;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    protected $bag;

    public function __construct(MessageBag $bag)
    {
        $this->bag = $bag;
    }

    public function login()
    {
        return view('auth.login');
    }

    public function attempt(PanelLoginRequest $request)
    {
    	$validated = $request->validated();

        $remember = $request->remember == 'On' ? true : false;

    	if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']], $remember)) {
            $this->bag->add('failed', 'Wrong credentials');
            $request->session()->flash('errors', $this->bag);

            return back()->withInput();
        }

        return redirect()->intended(route('panel.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

    	return redirect(route('auth.login'));
    }

    public function verify($key)
    {
        $user = UserTenant::where('verify_key', $key)->first();

        if (!$user) {
            return response('Invalid verification link');
        }

        if ($user) {
            $tenant = Tenant::where('id', $user->tenant_id)->first();
            $url = "http://". $tenant->domains->first()->domain ."/dashboard";
        }

        if ($user && !is_null($user->email_verified_at)) {
            return redirect($url);
        }

        if ($user && is_null($user->email_verified_at)) {
            $user->update(['email_verified_at' => now()]);

            return view('email-verified', compact('url'));
        }
    }
}
