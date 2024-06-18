<?php

namespace App\Services\Booking;

use App\Models\Booking\User;

class UserService
{
    public function user()
    {
        return auth()->user();
    }

    public function getDetails()
    {
        return [
            'id' => $this->user()->id,
            'email' => $this->user()->email,
            'camps' => json_decode($this->user()->allowed_camps, true),
            'commission' => $this->user()->commission_value,
        ];
    }

    public function is_agent()
    {
        return $this->user()?->hasRole('Agent');
    }

    public function getAgentList()
    {
        return User::query()
            ->orderBy('name', 'asc')
            ->get()
            ->filter(fn ($user) => $user->hasRole('Agent'))
            ->all();
    }

    public function all()
    {
        return User::orderBy('name')->get();
    }

    public function getInstructorOptionList()
    {
        return User::role('Instructor')->active()->orderBy('name')->pluck('name', 'id');
    }
}
