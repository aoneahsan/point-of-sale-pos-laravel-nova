<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Customer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Add Loyalty Points Request
 *
 * Validates data for adding loyalty points to a customer account.
 *
 * @property int $points Number of points to add (positive or negative)
 * @property string|null $reason Reason for adding/deducting points
 */
final class AddLoyaltyPointsRequest extends FormRequest
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
            'points' => [
                'required',
                'integer',
                'not_in:0',
                'min:-100000',
                'max:100000',
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
            'points.required' => 'Points amount is required.',
            'points.integer' => 'Points must be a whole number.',
            'points.not_in' => 'Points cannot be zero.',
        ];
    }
}
