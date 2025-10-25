<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Customer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Add Store Credit Request
 *
 * Validates data for adding store credit to a customer account.
 *
 * @property float $amount Credit amount to add (positive or negative)
 * @property string|null $reason Reason for adding/deducting credit
 */
final class AddStoreCreditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage-customers');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'not_in:0',
                'min:-999999.99',
                'max:999999.99',
            ],
            'reason' => [
                'nullable',
                'string',
                'max:255',
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
            'amount.required' => 'Credit amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.not_in' => 'Amount cannot be zero.',
        ];
    }
}
