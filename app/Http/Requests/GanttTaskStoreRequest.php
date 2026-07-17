<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GanttTaskStoreRequest extends FormRequest
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
            'proposal_id' => ['required', 'exists:proposals,id'],
            'text' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'hours' => ['nullable', 'numeric', 'min:0'],
            'priority_id' => ['required', 'exists:priorities,id'],
            'statu_id' => ['required', 'exists:status,id'],
            'progress' => ['nullable', 'numeric', 'between:0,1'],
            'parent' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'action' => 'error',
            'errors' => $validator->errors(),
        ], 422));
    }
}
