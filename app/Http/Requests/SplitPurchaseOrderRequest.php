<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SplitPurchaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Implement authorization logic here. For now, we'll allow all authenticated users.
        // It should check if the user has permission to 'create purchase_orders'
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'expected_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required_without:items.*.product_id|nullable|exists:purchase_order_items,id',
            'items.*.product_id' => 'required_without:items.*.item_id|nullable|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ];
    }
}
