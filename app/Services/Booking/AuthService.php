<?php

namespace App\Services\Booking;

use App\Models\Booking\User;
use App\Models\Booking\UserHistory;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class AuthService
{
    protected $bag;
    public $throttle_minutes;
    public $retry_limit;

    public function __construct(MessageBag $bag)
    {
        $this->bag = $bag;
        $this->throttle_minutes = 10;
        $this->retry_limit = 6;
    }

    public function isThrottled()
    {
        $ip = request()->ip();

        $date = Carbon::now();

        $time = $date->subMinutes($this->throttle_minutes)->format('Y-m-d H:i:s');

        $check = DB::table('user_histories')
            ->where('action', 'FAILED_LOGIN')
            ->where('ip_address', $ip)
            ->where('created_at', '>=', $time)
            ->count()
        ;

        return $check >= $this->retry_limit;
    }

    public function retryTime()
    {
        $ip = request()->ip();

        $check = DB::table('user_histories')
            ->where('action', 'FAILED_LOGIN')
            ->where('ip_address', $ip)
            ->orderBy('id', 'desc')
            ->first();

        $date = Carbon::createFromFormat('Y-m-d H:i:s', $check->created_at)->addMinutes(60);

        $now = Carbon::now();

        return $now->diffInMinutes($date);
    }

    public function attempt(Request $request)
    {
        $remember = 'On' == $request->remember ? true : false;

        try {
            $user = User::where('email', $request->email)->firstOrFail();
        } catch (\Exception $e) {
            $this->bag->add('failed', 'Wrong credentials');
            $request->session()->flash('errors', $this->bag);

            return back()->withInput();
        }

        if ($user->is_super && is_null($user->email_verified_at)) {
            $this->bag->add('failed', 'Please verify your email');
            $request->session()->flash('errors', $this->bag);

            return back()->withInput();
        }

        if (!$user = auth('tenant')->attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            $this->bag->add('failed', 'Wrong credentials');
            $request->session()->flash('errors', $this->bag);

            UserHistory::create([
                'user_id' => null,
                'email' => null,
                'action' => 'FAILED_LOGIN',
                'description' => 'Attempted email / password : '.$request->email.' / '.$request->password,
                'ip_address' => request()->ip(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return back()->withInput();
        }

        $user = auth('tenant')->user();

        if (!$user->is_active) {
            auth('tenant')->logout();

            $this->bag->add('failed', 'User is inactive');
            $request->session()->flash('errors', $this->bag);

            return redirect()->to(route('tenant.login'))->withInput();
        }

        if ($user->tenant_id == 'demo' && $user->expired_at < now()) {
            auth('tenant')->logout();

            $this->bag->add('failed', 'Demo user is expired');
            $request->session()->flash('errors', $this->bag);

            return redirect()->to(route('tenant.login'))->withInput();
        }

        $user->histories()->create([
            'email' => $user->email,
            'action' => 'SUCCESS_LOGIN',
            'description' => 'Successfully logged in',
            'ip_address' => request()->ip(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->intended(route('tenant.dashboard'));
    }

    public function logout()
    {
        auth('tenant')->user()->histories()->create([
            'user_id' => auth('tenant')->user()->id,
            'email' => auth('tenant')->user()->email,
            'action' => 'SUCCESS_LOGOUT',
            'description' => 'Successfully logged out',
            'ip_address' => request()->ip(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        auth('tenant')->logout();

        request()->session()->flash('messages', 'Logout successfully.');

        return redirect('auth/login');
    }
}
