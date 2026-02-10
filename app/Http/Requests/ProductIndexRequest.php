<?php

namespace App\Http\Requests;

use App\Rules\PriceRange;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'price_from' => ['nullable', 'numeric', 'min:0'],
            'price_to' => [
                'nullable',
                'numeric',
                'min:0',
                new PriceRange(),
            ],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'in_stock' => ['nullable', 'boolean'],
            'rating_from' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'sort' => ['nullable', Rule::in(\App\Constants\ProductConstants::SORT_OPTIONS)],
            'per_page' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Selected category does not exist.',
            'sort.in' => 'Invalid sort value.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normalize boolean values
        if ($this->has('in_stock')) {
            $this->merge([
                'in_stock' => filter_var($this->input('in_stock'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}
