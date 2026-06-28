<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VersionDeveloperCostsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'overrides' => ['present', 'array'],
            'overrides.*' => ['array:developer_id,cost_per_hour'],
            'overrides.*.developer_id' => ['required', 'integer', 'exists:developers,id'],
            'overrides.*.cost_per_hour' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
