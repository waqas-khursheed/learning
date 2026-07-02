<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Presentation layer ka hissa: input validation yahan isolate hoti hai
// taake Controller clean rahe.
class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'items'       => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'         => 'required|integer|min:1',
        ];
    }
}
