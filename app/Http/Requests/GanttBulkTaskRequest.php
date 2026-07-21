<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GanttBulkTaskRequest extends FormRequest
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
            'tasks' => ['required', 'array', 'min:1'],
            'tasks.*.text' => ['required', 'string', 'max:255'],
            'tasks.*.hours' => ['required', 'numeric', 'min:0'],
            'tasks.*.priority' => ['required', 'string', 'max:50'],
            'tasks.*.status' => ['required', 'string', 'max:50'],
            'tasks.*.developers' => ['nullable', 'array'],
            'tasks.*.developers.*.name' => ['required_without:tasks.*.developers.*.email', 'nullable', 'string', 'max:255'],
            'tasks.*.developers.*.email' => ['required_without:tasks.*.developers.*.name', 'nullable', 'email', 'max:255'],
            'tasks.*.developers.*.hours' => ['nullable', 'numeric', 'min:0'],
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
