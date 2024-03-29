<?php

namespace App\Http\Requests;

class RegisterRequest extends ApiRequest
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
            'email'     => 'required|email|unique:users',
            'password'  => 'required|confirmed|min:6',
            'name'      => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => "Provide a email"
        ];
    }
}
