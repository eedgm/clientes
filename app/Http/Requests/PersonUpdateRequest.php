<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonUpdateRequest extends FormRequest
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
            'photo' => ['nullable', 'file'],
            'phone' => ['nullable', 'max:255', 'string'],
            'skype' => ['nullable', 'max:255', 'string'],
            'client_id' => ['required', 'exists:clients,id'],
            'rol_id' => ['required', 'exists:rols,id'],
            'user_id' => ['required', 'exists:users,id'],
            'description' => ['nullable', 'max:255', 'string'],
        ];
    }
}
