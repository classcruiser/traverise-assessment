<?php

namespace App\Services\Booking;

use Carbon\Carbon;
use App\models\Booking\Location;

class LocationService
{
    public function get()
    {
        return Location::with(['rooms'])->orderBy('name', 'asc')->get();
    }

    public function getMinimumCheckIn(Location $camp): array
    {
        $min_nights = $camp->minimum_nights ?? 7;
        
        if (!empty($camp->minimum_checkin)) {
            // if minimum checkin is already passed the current date
            if (now()->gte($camp->minimum_checkin)) {
                // set minimum check in to today
                $start = now();
                $end = now()->addDays($min_nights);
                
                // check if camp has arrival rule
                if ($camp->has_arrival_rule) {
                    $start = $this->getNearestPossibleCheckInDate($camp);
                    $end = (new Carbon($start))->addDays($min_nights);

                    // check if there is arrival rule
                    if ($camp->rule->period) {
                        $temp_dates = explode(' - ', $camp->rule->period);
                        
                        $end_date = Carbon::createFromFormat('d.m.Y', $temp_dates[1]);

                        if ($start->gt($end_date)) {
                            // find the start date but add 1 year
                            $start = Carbon::createFromFormat('d.m.Y', $temp_dates[0])->addYear();
                            $end = (new Carbon($start))->addDays($min_nights);
                        }
                    }
                }
            } else {
                $start = new Carbon($camp->minimum_checkin);
                $end = (new Carbon($start))->addDays($min_nights);
            }
        } else {
            $start = now();
            $end = now()->addDays($min_nights);
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    public function getNearestPossibleCheckInDate(Location $camp): mixed
    {
        $disable_check_in_days = json_decode($camp->rule->disable_check_in_days, true);

        if (!$disable_check_in_days) {
            return now();
        }

        if (count($disable_check_in_days) >= 7) {
            return now();
        }

        for ($i = 0; $i < 7; $i++) {
            $date = now()->addDays($i);

            if (!in_array($date->format('l'), $disable_check_in_days)) {
                return $date;
            }
        }
    }
}
