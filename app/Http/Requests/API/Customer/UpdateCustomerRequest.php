<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Update Customer Request
 *
 * Validates data for updating an existing customer via API.
 * All fields are optional (partial updates allowed).
 *
 * @property string|null $name Customer full name
 * @property string|null $email Email address
 * @property string|null $phone Phone number
 * @property string|null $address Physical address
 * @property int|null $customer_group_id Customer group ID
 */
final class UpdateCustomerRequest extends FormRequest
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
        $customerId = $this->route('customer');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'email' => [
                'nullable',
                'string',
                'email:rfc,dns',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customerId),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],
            'address' => [
                'nullable',
                'string',
                'max:500',
            ],
            'customer_group_id' => [
                'nullable',
                'integer',
                'exists:customer_groups,id',
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
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'customer_group_id.exists' => 'Selected customer group does not exist.',
        ];
    }
}
