<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Product;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Store Product Request
 *
 * Validates data for creating a new product via API.
 * Ensures all required fields are present and properly formatted.
 *
 * @property string $name Product name
 * @property string $sku Stock Keeping Unit (unique identifier)
 * @property string|null $barcode Product barcode
 * @property string|null $description Product description
 * @property int $category_id Category ID
 * @property int|null $brand_id Brand ID (optional)
 * @property float $price Regular selling price
 * @property float|null $cost Cost price
 * @property int $store_id Store ID
 * @property bool $is_active Active status
 * @property bool $track_stock Whether to track inventory
 * @property int|null $stock_quantity Initial stock quantity
 * @property int|null $reorder_point Low stock alert threshold
 */
final class StoreProductRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'sku' => [
                'required',
                'string',
                'max:100',
                'unique:products,sku',
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                'unique:products,barcode',
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'brand_id' => [
                'nullable',
                'integer',
                'exists:brands,id',
            ],
            'price' => [
                'required',
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
            'store_id' => [
                'required',
                'integer',
                'exists:stores,id',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'track_stock' => [
                'sometimes',
                'boolean',
            ],
            'stock_quantity' => [
                'nullable',
                'integer',
                'min:0',
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
            'name.required' => 'Product name is required.',
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU already exists.',
            'barcode.unique' => 'This barcode already exists.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'Selected category does not exist.',
            'brand_id.exists' => 'Selected brand does not exist.',
            'price.required' => 'Product price is required.',
            'price.min' => 'Price cannot be negative.',
            'store_id.required' => 'Store ID is required.',
            'store_id.exists' => 'Selected store does not exist.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set defaults for boolean fields if not provided
        $this->merge([
            'is_active' => $this->input('is_active', true),
            'track_stock' => $this->input('track_stock', true),
        ]);
    }
}
