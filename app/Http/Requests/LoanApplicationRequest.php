<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanApplicationRequest extends FormRequest
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
            'loan_type_id' => 'required',
            'reason' => 'required|min:5|max:300',
            'bank_name' => 'required',
            'account_number' => 'required|digits_between:10,13',
            'account_type' => 'required',
            'full_name' => 'required|min:5|max:255',
            'address' => 'required|max:255',
            'phone' => 'required|digits_between:10,15',
            'email' => 'email'
        ];
    }
}
