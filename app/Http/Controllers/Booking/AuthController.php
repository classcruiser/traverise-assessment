<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Services\Booking\AuthService;
use App\Http\Requests\Booking\AdminLogin;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index()
    {
        if ($this->authService->isThrottled()) {
            $retry = $this->authService->retryTime();
            return view('Booking.throttled', compact('retry'));
        }

        return view('Booking.auth');
    }

    public function login(AdminLogin $request)
    {
        if ($this->authService->isThrottled()) {
            $retry = $this->authService->retryTime();
            return view('Booking.throttled', compact('retry'));
        }

        return $this->authService->attempt($request);
    }

    public function logout()
    {
        return $this->authService->logout();
    }
}
