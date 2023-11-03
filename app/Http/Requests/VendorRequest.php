<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class VendorRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|string',
            'category' => 'required|string',
            'grade' => 'nullable|string',
            'notes' => 'nullable|string',
            'phone_number' => 'nullable|string',
//            'email' => 'nullable|string',
//            'mobile' => 'nullable|string',
            'company_name' => 'nullable|string',
            'address' => 'nullable|string',
            'priority' => 'nullable|string',
        ];

        if(request()->getMethod() == "PUT"){
            $rules['email'] = ['nullable', 'string', 'email', 'max:255', 'unique:vendors,email,'.$this->vendor->id];
            $rules['mobile'] = ['nullable', 'string', 'unique:vendors,mobile,'.$this->vendor->id];
        }else{
            $rules['email'] = ['nullable', 'string', 'email', 'max:255', 'unique:vendors'];
            $rules['mobile'] = ['nullable', 'string', 'unique:vendors'];
        }

        return $rules;
    }
}
