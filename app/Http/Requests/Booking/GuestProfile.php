<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class GuestProfile extends FormRequest
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
            'email' => 'required|unique:guests,email,' . request('guest_id'),
            'birthdate_day' => 'required',
            'birthdate_month' => 'required',
            'birthdate_year' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'country' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'fname.required' => 'First name is required.',
            'lname.required' => 'Last name is required.',
            'email.unique' => 'Guest with this email address is already exist.',
            'birthdate_day.required' => 'Birthdate (day) is required',
            'birthdate_month.required' => 'Birthdate (month) is required',
            'birthdate_year.required' => 'Birthdate (year) is required',
        ];
    }
}
