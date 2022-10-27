<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientStoreRequest extends FormRequest
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
            'logo' => ['image', 'max:1024', 'nullable'],
            'name' => ['required', 'max:255', 'string'],
            'cost_per_hour' => ['required', 'numeric'],
            'owner' => ['required', 'max:255', 'string'],
            'email_contact' => ['required', 'max:255', 'string'],
            'description' => ['nullable', 'max:255', 'string'],
        ];
    }
}
