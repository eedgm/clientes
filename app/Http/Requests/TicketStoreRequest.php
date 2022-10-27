<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketStoreRequest extends FormRequest
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
            'description' => ['required', 'max:255', 'string'],
            'statu_id' => ['required', 'exists:status,id'],
            'priority_id' => ['required', 'exists:priorities,id'],
            'hours' => ['nullable', 'numeric'],
            'total' => ['nullable', 'numeric'],
            'finished_ticket' => ['nullable', 'date'],
            'comments' => ['nullable', 'max:255', 'string'],
            'product_id' => ['required', 'exists:products,id'],
            'receipt_id' => ['nullable', 'exists:receipts,id'],
            'person_id' => ['nullable', 'exists:people,id'],
        ];
    }
}
