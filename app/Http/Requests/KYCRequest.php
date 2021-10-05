<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KYCRequest extends FormRequest
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
            'user_id' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'city' => 'required',
            'address' => 'required',
            'identification_type' => 'required',
            'id_number' => 'required|numeric|min:10',
            'nok_first_name' => 'required|min:2|max:255',
            'nok_last_name' => 'required|min:2|max:255',
            'nok_email' => 'email',
            'nok_phone' => 'required|numeric',
            'nok_country_id' => 'required',
            'nok_state_id' => 'required'
        ];
    }
}
