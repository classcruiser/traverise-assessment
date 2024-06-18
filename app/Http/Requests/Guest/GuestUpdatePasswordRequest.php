<?php

namespace App\Http\Requests\Guest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GuestUpdatePasswordRequest extends FormRequest
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
            'token' => 'required',
            'email' => 'required|email|exists:guests',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => '',
            'errors'      => $validator->errors()
        ]));
    }

    public function messages()
    {
        return [
            'email.required' => 'Email cannot be blank',
            'email.email' => 'Email must be valid',
            'email.exists' => 'Email not found',
            'password.required' => 'Password cannot be blank',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Passwords do not match',
            'token.required' => 'Token cannot be blank',
        ];
    }
}
