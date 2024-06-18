<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormTenantRequest extends FormRequest
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
            'id' => 'required|unique:tenants',
            'plan' => 'required',
            'first_name' => 'required',
            'email' => 'required|email|unique:tenants',
            'phone' => 'required',
            'country' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'id.unique' => 'The domain is already exist',
            'first_name.required' => 'Please enter your first name',
            'email.required' => 'Please enter your email address',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'Email address is already exist',
            'phone.required' => 'Please enter your phone number',
            'country.required' => 'Please select your country of residence'
        ];
    }
}
