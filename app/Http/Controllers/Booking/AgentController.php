<?php

namespace App\Http\Controllers\Booking;

use App\Models\Booking\User;

use Illuminate\Http\Request;
use App\Models\Booking\Location;
use App\Models\Booking\AgentRoom;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AgentController extends Controller
{
    /**
     * AGENT INDEX.
     *
     * @param none
     *
     * @return Illuminate\Http\View
     */
    public function index()
    {
        // $agents = User::with(['rooms'])->where('role_id', 4)->orderBy('name', 'asc')->get();
        $agents = User::query()
            ->with(['rooms'])
            ->orderBy('name', 'asc')
            ->get()
            ->filter(fn ($user) => $user->hasRole('Agent'))
            ->all();

        return view('Booking.agents.index', compact('agents'));
    }

    public function show($id)
    {
        $agent = User::with(['rooms'])->find($id);

        $locations = Location::with(['rooms' => function ($q) {
            $q->where('active', 1);
        }, 'rooms.rooms'])->get();

        return view('Booking.agents.show', compact('agent', 'locations', 'id'));
    }

    public function create()
    {
        $locations = Location::with(['rooms' => function ($q) {
            $q->where('active', 1);
        }, 'rooms.rooms'])->get();

        return view('Booking.agents.new', compact('locations'));
    }

    public function insert()
    {
        DB::beginTransaction();

        try {
            $check = User::where('email', request('email'))->where('id', '!=', auth()->user()->id)->count();

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
            'commission_value' => floatVal(request('commission_value')),
        ]);

        $taxes = [
            request('hotel_tax') == 'on' ? 1 : 0,
            request('goods_tax') == 'on' ? 1 : 0,
        ];
        
        $user->update(['tax_setting' => implode(',', $taxes)]);

        $user->syncRoles('Agent');

        DB::commit();

        session()->flash('messages', 'Agent added');

        return redirect(route('tenant.agents.show', $user->id));
    }

    public function update($id)
    {
        DB::beginTransaction();

        $agent = User::with(['rooms'])->find($id);

        $agent->update(request()->only(['name', 'commission_value']));

        $agent->refresh();

        if ('' != request('password')) {
            $agent->update([
                'password' => bcrypt(request('password')),
            ]);
        }

        $rooms = collect(request('rooms'))->keys()->toArray();
        $agent->update(['is_active' => request()->has('active')]);

        $sp = AgentRoom::where('user_id', $id)->whereNotIn('room_id', $rooms)->delete();

        if (request()->has('rooms') && count(request()->rooms) > 0) {
            foreach (request('rooms') as $subroom_id => $state) {
                AgentRoom::firstOrCreate([
                    'user_id' => $agent->id,
                    'room_id' => $subroom_id,
                ]);
            }
        }

        $taxes = [
            request('hotel_tax') == 'on' ? 1 : 0,
            request('goods_tax') == 'on' ? 1 : 0,
        ];
        
        $agent->update(['tax_setting' => implode(',', $taxes)]);

        DB::commit();

        session()->flash('messages', 'Agent updated');

        return redirect('agents/'.$id);
    }

    public function delete($id)
    {
        if (1 !== Auth::user()->id) {
            return redirect('users');
        }

        User::find($id)->delete();

        session()->flash('messages', 'User deleted');

        return redirect('users/');
    }
}
