<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client.name' => ['required', 'string'],
            'client.email' => ['required', 'email'],

            'products' => ['required', 'array'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],

            'card_number' => ['required', 'string', 'size:16'],
            'cvv' => ['required', 'string', 'size:3']
        ];
    }
}