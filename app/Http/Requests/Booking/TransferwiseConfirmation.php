<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class TransferwiseConfirmation extends FormRequest
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
      'proof' => 'required|image'
    ];
  }

  public function messages()
  {
    return [
      '*.required' => 'is required',
      'proof.image' => 'is invalid'
    ];
  }
}
