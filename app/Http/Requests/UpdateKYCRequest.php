<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKYCRequest extends FormRequest
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
            'id_number' => 'numeric|min:10|max:12',
            'nok_first_name' => 'min:2|max:255',
            'nok_last_name' => 'min:2|max:255',
            'nok_email' => 'email',
            'nok_phone' => 'numeric|max:15',
        ];
    }
}
