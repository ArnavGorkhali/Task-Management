<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
//            'email' => 'nullable|string',
            'company_name' => 'nullable|string',
//            'mobile' => 'nullable|string',
        ];

        if(request()->getMethod() == "PUT"){
            $rules['email'] = ['nullable', 'string', 'email', 'max:255', 'unique:clients,email,'.$this->client->id];
            $rules['mobile'] = ['nullable', 'string', 'unique:clients,mobile,'.$this->client->id];
        }else{
            $rules['email'] = ['nullable', 'string', 'email', 'max:255', 'unique:clients'];
            $rules['mobile'] = ['nullable', 'string', 'unique:clients'];
        }

        return $rules;
    }
}
