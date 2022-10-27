<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachUpdateRequest extends FormRequest
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
            'attachment' => ['file', 'max:1024', 'required'],
            'description' => ['nullable', 'max:255', 'string'],
            'task_id' => ['required', 'exists:tasks,id'],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }
}
