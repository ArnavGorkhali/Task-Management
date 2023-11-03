<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FunctionRequest extends FormRequest
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
            'event_id' => 'required|int|exists:events,id',
            'start_date' => 'nullable|date_format:Y-m-d H:i:s',
            'end_date' => 'nullable|date_format:Y-m-d H:i:s',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
            'color_code' => 'nullable|array',
            'venue_id' => 'nullable|int|exists:venues,id'
        ];
    }
}
