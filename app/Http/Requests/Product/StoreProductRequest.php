<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductStatus;
use App\Http\Requests\ApiRequest;
use App\Services\Product\DTOs\ProductDTO;
use Illuminate\Validation\Rules\Enum;


class StoreProductRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'desc' => 'string',
            'count' => 'required|integer',
            'price' => 'required|numeric',
            'state' => ['required', new Enum(ProductStatus::class)],
            'images' => 'array',
        ];
    }

    public function data(): ProductDTO
    {
        return ProductDTO::from($this->validated());
    }
}
