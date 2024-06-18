<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class CovidGuestDetails extends FormRequest
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
      'title' => 'required',
      'email' => 'required',
      'birthdate_day' => 'required',
      'birthdate_month' => 'required',
      'birthdate_year' => 'required',
      'street' => 'required',
      'city' => 'required',
      'zip' => 'required',
      'country' => 'required',
      'phone' => 'required_if:skip_transfer,',
      'terms' => 'required',
      'arrival_time_h' => 'required_if:skip_transfer,',
      'arrival_time_m' => 'required_if:skip_transfer,',
      'arrival_flight' => 'required_if:skip_transfer,',
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
      'phone.required' => 'WhatsApp number is required',
      'arrival_time_h.required_if' => 'Arrival time is required',
      'arrival_time_m.required_if' => 'Arrival time is required',
      'arrival_flight.required_if' => 'Arrival flight info required',
      'terms.required' => 'You must agree to our terms and conditions',
    ];
  }
}
