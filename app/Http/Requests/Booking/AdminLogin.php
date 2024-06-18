<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class AdminLogin extends FormRequest
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

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'email' => 'required|max:255',
      'password' => 'required'
    ];
  }

  public function messages()
  {
    return [
      'email.required' => 'Please enter your email.',
      'password.required' => 'Please enter your passowrd.'
    ];
  }
}
