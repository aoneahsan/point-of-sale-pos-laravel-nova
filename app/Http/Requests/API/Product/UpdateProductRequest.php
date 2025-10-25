<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Update Product Request
 *
 * Validates data for updating an existing product via API.
 * All fields are optional (partial updates allowed).
 *
 * @property string|null $name Product name
 * @property string|null $sku Stock Keeping Unit
 * @property string|null $barcode Product barcode
 * @property string|null $description Product description
 * @property int|null $category_id Category ID
 * @property int|null $brand_id Brand ID
 * @property float|null $price Selling price
 * @property float|null $cost Cost price
 * @property bool|null $is_active Active status
 * @property bool|null $track_stock Track inventory
 * @property int|null $reorder_point Low stock threshold
 */
final class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage-products');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'sku' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'barcode')->ignore($productId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'category_id' => [
                'sometimes',
                'integer',
                'exists:categories,id',
            ],
            'brand_id' => [
                'nullable',
                'integer',
                'exists:brands,id',
            ],
            'price' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'cost' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'track_stock' => [
                'sometimes',
                'boolean',
            ],
            'reorder_point' => [
                'nullable',
                'integer',
                'min:0',
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
            'sku.unique' => 'This SKU already exists.',
            'barcode.unique' => 'This barcode already exists.',
            'category_id.exists' => 'Selected category does not exist.',
            'brand_id.exists' => 'Selected brand does not exist.',
            'price.min' => 'Price cannot be negative.',
        ];
    }
}
