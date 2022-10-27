<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayableStoreRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'cost' => ['required', 'numeric'],
            'margin' => ['required', 'numeric'],
            'total' => ['required', 'numeric'],
            'product_id' => ['required', 'exists:products,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'supplier_id_reference' => ['nullable', 'max:255', 'string'],
            'periodicity' => ['required', 'in:month,year'],
            'receipt_id' => ['nullable', 'exists:receipts,id'],
        ];
    }
}
