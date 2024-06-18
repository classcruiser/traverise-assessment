<?php

namespace App\View\Composers;

use App\Models\Booking\Booking;
use App\Models\Booking\Location;
use App\Models\Classes\ClassBooking;
use App\Models\Classes\ClassCategory;
use App\Models\Classes\ClassSession;
use App\Services\Booking\UserService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HeaderComposer
{
    public function compose(View $view)
    {
        $is_agent = (new UserService())->is_agent();

        $sessions = null;
        $class_categories = null;
        $menu_camps = null;

        if (tenant('plan') != 'events') {
            $total_drafts = Cache::remember('total_draft_booking', 1, function () use ($is_agent) {
                return Booking::query()
                    ->when($is_agent, fn ($q) => $q->where('agent_id', auth()->user()->id))
                    ->where('status', 'DRAFT')
                    ->count();
            });

            $total_pendings = Cache::remember('total_pending_booking', 1, function () use ($is_agent) {
                return Booking::query()
                    ->when($is_agent, fn ($q) => $q->where('agent_id', auth()->user()->id))
                    ->where(function ($q) {
                        $q
                            ->where('status', 'PENDING')
                            ->orWhere('status', 'ABANDONED')
                            ->orWhere('status', 'RESERVED');
                    })
                    ->count();
            });

            $menu_camps = Cache::remember('menu_camps', 5, function () {
                return Location::orderBy('name', 'asc')->get();
            });
        } else {
            $total_drafts = Cache::remember('total_draft_booking', 1, function () use ($is_agent) {
                return ClassBooking::query()
                    ->where('status', 'DRAFT')
                    ->count();
            });

            $total_pendings = Cache::remember('total_pending_booking', 1, function () use ($is_agent) {
                return ClassBooking::query()
                    ->where(function ($q) {
                        $q
                            ->where('status', 'PENDING')
                            ->orWhere('status', 'ABANDONED')
                            ->orWhere('status', 'RESERVED');
                    })
                    ->count();
            });

            $sessions = Cache::remember('class_categories', 5, function () {
                return ClassCategory::with(['sessions' => function ($q) {
                        $q->orderBy('name', 'asc')->select(['id', 'class_category_id', 'name']);
                    }])->get(['id', 'name']);
            });

        }

        $view->with('total_pendings', $total_pendings);
        $view->with('menu_camps', $menu_camps);
        $view->with('total_drafts', $total_drafts);
        $view->with('sessions', $sessions);
    }
}
