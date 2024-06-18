<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class GuestDetails extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required',
            'birthdate_day' => 'required',
            'birthdate_month' => 'required',
            'birthdate_year' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'phone' => 'required|numeric',
            'arrival_time_h' => 'required_unless:skip_transfer,on',
            'arrival_time_m' => 'required_unless:skip_transfer,on',
            'arrival_flight' => 'required_unless:skip_transfer,on',
            'departure_time_h' => 'required_unless:skip_transfer,on',
            'departure_time_m' => 'required_unless:skip_transfer,on',
            'departure_flight' => 'required_unless:skip_transfer,on',
            'guest.*.fname' => 'required',
            'guest.*.lname' => 'required',
            'guest.*.email' => 'required',
            'guest.*.birthdate_day' => 'required',
            'guest.*.birthdate_month' => 'required',
            'guest.*.birthdate_year' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'fname.required' => 'First name is required.',
            'lname.required' => 'Last name is required.',
            'birthdate_day.required' => 'Birthdate (day) is required',
            'birthdate_month.required' => 'Birthdate (month) is required',
            'birthdate_year.required' => 'Birthdate (year) is required',
            'phone.required' => 'Phone number is required',
            'arrival_time_h.required_unless' => 'Arrival time is required',
            'arrival_time_m.required_if' => 'Arrival time is required',
            'arrival_flight.required_if' => 'Arrival flight info required',
            'departure_time_h.required_if' => 'Departure time is required',
            'departure_time_m.required_if' => 'Departure time is required',
            'departure_flight.required_if' => 'Departure flight info required',
        ];
    }
}
