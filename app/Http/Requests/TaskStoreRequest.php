<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
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
            'name' => ['required', 'max:255', 'string'],
            'hours' => ['required', 'numeric'],
            'statu_id' => ['required', 'exists:status,id'],
            'priority_id' => ['required', 'exists:priorities,id'],
            'real_hours' => ['nullable', 'numeric'],
            'version_id' => ['required', 'exists:versions,id'],
            'receipt_id' => ['nullable', 'exists:receipts,id'],
        ];
    }
}
