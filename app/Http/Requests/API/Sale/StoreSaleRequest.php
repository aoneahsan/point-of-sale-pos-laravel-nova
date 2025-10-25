<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Store Sale Request
 *
 * Validates data for creating a new sale transaction via API.
 * Implements comprehensive validation for sale items, payments, and customer info.
 *
 * @property int $store_id Store ID
 * @property int|null $customer_id Customer ID (optional)
 * @property array $items Array of sale items
 * @property array $payments Array of payment methods
 * @property int|null $discount_id Applied discount ID
 * @property string|null $coupon_code Applied coupon code
 * @property string|null $notes Sale notes
 */
final class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('process-sales');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'store_id' => [
                'required',
                'integer',
                'exists:stores,id',
            ],
            'customer_id' => [
                'nullable',
                'integer',
                'exists:customers,id',
            ],

            // Sale items validation
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],
            'items.*.product_variant_id' => [
                'nullable',
                'integer',
                'exists:product_variants,id',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],
            'items.*.price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'items.*.discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            // Payments validation
            'payments' => [
                'required',
                'array',
                'min:1',
            ],
            'payments.*.payment_method_id' => [
                'required',
                'integer',
                'exists:payment_methods,id',
            ],
            'payments.*.amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
            ],
            'payments.*.reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            // Discount validation
            'discount_id' => [
                'nullable',
                'integer',
                'exists:discounts,id',
            ],
            'coupon_code' => [
                'nullable',
                'string',
                'max:50',
                'exists:coupons,code',
            ],

            // Notes
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'store_id.required' => 'Store ID is required.',
            'store_id.exists' => 'Selected store does not exist.',
            'customer_id.exists' => 'Selected customer does not exist.',

            'items.required' => 'At least one item is required.',
            'items.min' => 'At least one item is required.',
            'items.*.product_id.required' => 'Product ID is required for each item.',
            'items.*.product_id.exists' => 'One or more products do not exist.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.price.required' => 'Price is required for each item.',

            'payments.required' => 'At least one payment method is required.',
            'payments.min' => 'At least one payment method is required.',
            'payments.*.payment_method_id.required' => 'Payment method is required.',
            'payments.*.payment_method_id.exists' => 'Invalid payment method.',
            'payments.*.amount.required' => 'Payment amount is required.',
            'payments.*.amount.min' => 'Payment amount must be greater than zero.',

            'coupon_code.exists' => 'Invalid coupon code.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that total payment amount matches sale total
            if ($this->has('items') && $this->has('payments')) {
                $itemsTotal = collect($this->items)->sum(function ($item) {
                    $lineTotal = $item['quantity'] * $item['price'];
                    return $lineTotal - ($item['discount_amount'] ?? 0);
                });

                $paymentsTotal = collect($this->payments)->sum('amount');

                // Allow small rounding differences (1 cent)
                if (abs($itemsTotal - $paymentsTotal) > 0.01) {
                    $validator->errors()->add(
                        'payments',
                        'Total payment amount must match the sale total.'
                    );
                }
            }
        });
    }
}
