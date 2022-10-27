<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceiptStoreRequest extends FormRequest
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
            'real_date' => ['required', 'date'],
            'number' => ['required', 'numeric'],
            'description' => ['nullable', 'max:255', 'string'],
            'client_id' => ['required', 'exists:clients,id'],
            'charged' => ['required', 'boolean'],
            'reference_charged' => ['nullable', 'max:255', 'string'],
            'date_charged' => ['nullable', 'date'],
        ];
    }
}
