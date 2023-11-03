<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileRequest extends FormRequest
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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string'],
            'profile_pic' => 'nullable|image|mimes:jpg,png,jpeg|max:10000',
        ];

        if(request()->getMethod() == "PUT"){
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email,'.Auth::id()];
        }else{
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users'];
        }

        return $rules;
    }
}
