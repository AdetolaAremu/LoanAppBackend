<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email',
            // 'username' => 'required|min:5|max:255',
            'phone' => 'required|numeric|digits_between:10,15',
            'password' => 'required|min:4|max:50',
            'confirm_password' => 'required|same:password'
        ];
    }
}
