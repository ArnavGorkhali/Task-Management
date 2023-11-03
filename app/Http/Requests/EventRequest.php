<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
//            'email' => 'nullable|email',
            'note' => 'nullable|string',
            'ethnicity' => 'required|string',
            'company_name' => 'nullable|string',
            'company_id' => 'nullable|string',
            'category' => 'required|string',
            'client_id' => 'nullable|int',
            'client_name' => 'nullable|string',
//            'mobile_number' => 'nullable|string',
            'start_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date' => 'required|date_format:Y-m-d H:i:s',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
        ];

        if(request()->getMethod() == "PUT"){
            $rules['email'] = ['nullable', 'email'];
            $rules['mobile_number'] = ['nullable', 'string'];
        }else{
            $rules['email'] = ['nullable', 'email'];
            $rules['mobile_number'] = ['nullable', 'string'];
        }

        return $rules;
    }
}
