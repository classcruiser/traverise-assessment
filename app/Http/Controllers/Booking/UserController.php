<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking\Location;
use App\Models\Booking\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Booking\Permission;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name', 'asc')->get();

        $locations = Location::all()->keyBy('id');

        return view('Booking.users.index', compact('users', 'locations'));
    }

    public function show($id)
    {
        $user = User::find($id);

        $locations = Location::all()->keyBy('id');

        $roles = Role::whereGuardName('tenant')->where('tenant_id', tenant('id'))->where('name', '!=', 'Super Admin')->get();

        $permissions = Permission::whereGuardName('tenant')->get();

        return view('Booking.users.show', compact('user', 'locations', 'id', 'roles', 'permissions'));
    }

    public function update($id)
    {
        DB::beginTransaction();

        try {
            $check = User::where('email', request('email'))->where('id', '!=', $id)->count();

            if ($check > 0) {
                throw new \Exception('Email address already exists');
            }
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());

            return redirect()->back()->withInput();
        }

        $user = User::find($id);

        $user->update(request()->only([
            'name', 'email',
        ]));

        $camps = collect(request('allowed_camps')) ?? collect([]);

        $allowed_camps = '['.($camps->keys()->implode(', ')).']';

        if (request()->has('password') && request('password') != '') {
            $user->update(['password' => bcrypt(request('password'))]);
        }

        $user->update([
            'allowed_camps' => $allowed_camps,
            'is_active' => request()->has('is_active'),
            'can_drag' => request()->has('can_drag'),
        ]);

        if (request()->has('role') && request('role') != '') {
            $user->syncRoles(request('role'));
        }

        DB::commit();

        session()->flash('messages', 'User updated');

        return redirect('users/'.$id);
    }

    public function create()
    {
        $locations = Location::all()->keyBy('id');

        $roles = Role::whereGuardName('tenant')->where('tenant_id', tenant('id'))->where('name', '!=', 'Super Admin')->get();

        $permissions = Permission::whereGuardName('tenant')->get();

        return view('Booking.users.new', compact('locations', 'roles', 'permissions'));
    }

    public function insert()
    {
        DB::beginTransaction();

        try {
            $check = User::where('email', request('email'))->where('id', '!=', Auth::user()->id)->count();

            if ($check > 0) {
                throw new \Exception('Email address already exists');
            }
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());

            return redirect()->back()->withInput();
        }

        $permissions = collect(request('permissions'))->keys();

        $camps = collect(request('allowed_camps')) ?? collect([]);

        $allowed_camps = '['.($camps->keys()->implode(', ')).']';

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'is_active' => request()->has('is_active'),
            'password' => bcrypt(request('password')),
            'allowed_camps' => $allowed_camps,
            'can_drag' => request()->has('can_drag'),
        ]);

        $user->assignRole(request('role'));
        $user->givePermissionTo($permissions);

        DB::commit();

        session()->flash('messages', 'User added');

        return $user->hasRole('Agents') ? redirect(route('tenant.agents.show', $user->id)) : redirect(route('tenant.users.show', $user->id));
    }

    public function delete($id)
    {
        $user = User::find($id);

        if (!$user->is_super) {
            $user->delete();

            session()->flash('messages', 'User deleted');
        }

        return redirect(route('tenant.users'));
    }
}
