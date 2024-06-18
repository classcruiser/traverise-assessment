<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class TransferDetails extends FormRequest
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
      'confirmed' => 'required',
      'flight_number.*' => 'required',
      'flight_time.*' => 'required',
    ];
  }

  public function messages()
  {
    return [
      'flight_number.*.required' => 'Flight number is required.',
      'flight_time.*.required' => 'Flight time is required.',
      'confirmed.required' => 'Make sure you entered the correct transfer details.',
    ];
  }
}
