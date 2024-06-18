<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class TenantAuthenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            //abort(401);
            return route('tenant.login');
        }
    }
}
