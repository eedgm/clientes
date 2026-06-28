<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GanttTaskDevelopersRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'developers' => ['present', 'array'],
            'developers.*' => ['array:developer_id,hours'],
            'developers.*.developer_id' => ['required', 'integer', 'exists:developers,id'],
            'developers.*.hours' => ['nullable', 'numeric', 'min:0'],
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
