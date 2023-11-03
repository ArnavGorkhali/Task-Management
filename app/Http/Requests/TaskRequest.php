<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
        return [
            'name' => 'required|string',
            'note' => 'nullable|string',
            'parent_id' => 'required_without:function_id|int|exists:tasks,id',
            'function_id' => 'required_without:parent_id|int|exists:functions,id',
            'vendor_id' => 'nullable|int|exists:vendors,id',
            'start_date' => 'nullable|date_format:Y-m-d H:i:s',
            'end_date' => 'nullable|date_format:Y-m-d H:i:s',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
        ];
    }
}
