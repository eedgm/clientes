<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatuUpdateRequest extends FormRequest
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
            'limit' => ['required', 'numeric'],
            'color_id' => ['required', 'exists:colors,id'],
            'icon_id' => ['required', 'exists:icons,id'],
        ];
    }
}
