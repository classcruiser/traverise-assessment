<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Booking\User;
use Illuminate\Http\Request;
use App\Models\Booking\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DemoController extends Controller
{
	public function registerDemoUser(Request $request)
	{
		$validated = $request->validate([
			'full_name' => 'required',
			'email' => 'required|email'
		]);

		$check = User::where('tenant_id', 'demo')
			->where('email', $validated['email'])
			->first();

		if ($check) {
			return response(['status' => 'EMAIL_EXIST']);
		}

		$camps = Location::where('tenant_id', 'demo')->where('active', 1)->get();

		DB::beginTransaction();

		$password = strtoupper(Str::random(6));

		$user = User::create([
			'tenant_id' => 'demo',
			'name' => $validated['full_name'],
			'email' => $validated['email'],
			'email_verified_at' => now(),
			'password' => bcrypt($password),
			'is_active' => 1,
			'is_super' => 0,
			'can_drag' => 1,
			'allowed_camps' => '['. $camps->map(fn ($camp) => $camp->id)->implode(', ') .']',
			'expired_at' => now()->addDays(7),
		]);

		$user->assignRole('Demo');

		// email user
		try {
			Mail::to($user->email)
				->cc('olli@traverise.com')
				->send(new \App\Mail\DemoUserSignedUp($user, $password));
		} catch (\Exception $e) {
			Log::error($e->getMessage());
		}

		DB::commit();

		return response(['status' => 'OK']);
	}
}