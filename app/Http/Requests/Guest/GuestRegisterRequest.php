<?php

namespace App\Http\Requests\Guest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GuestRegisterRequest extends FormRequest
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
            'fname' => 'nullable',
            'lname' => 'nullable',
            'email' => 'required|email',
            'phone' => 'nullable|numeric',
            'country' => 'nullable',
            'street' => 'nullable',
            'password' => 'required|min:8',
            'passwordRepeat' => 'same:password',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'errors'      => $validator->errors()
        ]));
    }

    public function messages()
    {
        return [
            'fname.required' => 'Please enter your first name',
            'lname.required' => 'Please enter your last name',
            'email.required' => 'Please enter your email address',
            'email.email' => 'Please enter a valid email address',
            'phone.numeric' => 'Please a valid phone number',
            'password.required' => 'Please enter your password',
            'password.min' => 'Password must have at least :min characters',
            'passwordRepeat.same' => 'Passwords must be the same',
        ];
    }
}
