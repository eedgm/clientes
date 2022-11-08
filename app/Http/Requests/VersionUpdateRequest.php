<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VersionUpdateRequest extends FormRequest
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
            'user_id' => ['required', 'exists:users,id'],
            'attachment' => ['file', 'max:1024', 'nullable'],
            'total' => ['required', 'numeric'],
            'time' => ['nullable', 'numeric'],
            'cost_per_hour' => ['required', 'numeric'],
            'hour_per_day' => ['required', 'numeric'],
            'months_to_pay' => ['required', 'numeric'],
            'unexpected' => ['required', 'numeric'],
            'company_gain' => ['required', 'numeric'],
            'bank_tax' => ['required', 'numeric'],
            'first_payment' => ['required', 'numeric'],
            'hours' => ['nullable', 'numeric'],
        ];
    }
}
