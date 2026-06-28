<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GanttTaskUpdateRequest extends FormRequest
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
            'proposal_id' => ['nullable', 'exists:proposals,id'],
            'text' => ['sometimes', 'required', 'string', 'max:255'],
            'start_date' => ['sometimes', 'required', 'date'],
            'duration' => ['sometimes', 'required', 'integer', 'min:0'],
            'hours' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'priority_id' => ['sometimes', 'required', 'exists:priorities,id'],
            'statu_id' => ['nullable', 'exists:status,id'],
            'progress' => ['nullable', 'numeric', 'between:0,1'],
            'parent' => ['nullable', 'integer', 'min:0'],
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
