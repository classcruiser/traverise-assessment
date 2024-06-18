<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking\Role;
use App\Models\Booking\Permission;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Role::whereGuardName('tenant')->get();

        if (request()->has('clear')) {
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }

        return view('Booking.roles.index', compact('roles'));
    }

    public function show($id)
    {
        $role = Role::find($id);

        $permissions = Permission::whereGuardName('tenant')->whereNull('tenant_id')->withoutTenancy()->get();

        return view('Booking.roles.show', compact('role', 'permissions'));
    }

    public function create()
    {
        $permissions = Permission::whereGuardName('tenant')->whereNull('tenant_id')->withoutTenancy()->get();

        return view('Booking.roles.new', compact('permissions'));
    }

    public function insert()
    {
        try {
            $permissions = collect(request('permissions'))->keys();

            $role = Role::create([
                'guard_name' => 'tenant',
                'name' => request('name'),
                'tenant_id' => tenant()->id,
            ]);

            $role->givePermissionTo($permissions);

            session()->flash('messages', 'Role added');

            return redirect(route('tenant.roles.show', $role->id));
        } catch (\Spatie\Permission\Exceptions\RoleAlreadyExists $e) {
            session()->flash('error', $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    public function update($id)
    {
        try {
            $permissions = collect(request('permissions'))->keys();

            $role = Role::findById($id);

            $role->update(['name' => request('name')]);

            $role->syncPermissions($permissions);

            session()->flash('messages', 'Role updated');

            return redirect(route('tenant.roles.show', $role->id));
        } catch (\Exception $e) {
        }
    }

    public function delete($id)
    {
        $role = Role::findById($id);

        $role->delete();

        session()->flash('messages', 'Role deleted');

        return redirect(route('tenant.roles'));
    }
}
