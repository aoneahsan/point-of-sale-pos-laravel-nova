<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Sale;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Refund Sale Request
 *
 * Validates data for processing a refund/return via API.
 *
 * @property array $items Items to refund
 * @property string $reason Refund reason
 * @property string|null $notes Additional notes
 */
final class RefundSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('process-refunds');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.sale_item_id' => [
                'required',
                'integer',
                'exists:sale_items,id',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'items.*.reason' => [
                'required',
                'string',
                'in:defective,wrong_item,customer_request,damaged,other',
            ],
            'reason' => [
                'required',
                'string',
                'max:500',
            ],
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
            'items.required' => 'At least one item must be selected for refund.',
            'items.min' => 'At least one item must be selected for refund.',
            'items.*.sale_item_id.required' => 'Sale item ID is required.',
            'items.*.sale_item_id.exists' => 'One or more sale items do not exist.',
            'items.*.quantity.required' => 'Refund quantity is required for each item.',
            'items.*.quantity.min' => 'Refund quantity must be at least 1.',
            'items.*.reason.required' => 'Reason is required for each refunded item.',
            'items.*.reason.in' => 'Invalid refund reason.',
            'reason.required' => 'Overall refund reason is required.',
        ];
    }
}
